<?php

namespace App\Http\Controllers\Api;

use App\Mail\SendCodeResetPassword;
use App\Mail\SendLinkVerifyEmail;
use App\Models\VertifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class StudentAuth extends Controller
{
    /**
     * Get a JWT token via given credentials.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|exists:student,code',
            'password' => 'required|string'
        ], [
            'code.exists' => 'Student code does not exist'
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json([
                'success' => 0,
                'message' => $validator->errors()->first(),
                'data' => []], 200);
        }

        $credentials = $request->only('code', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'success' => 1,
                'message' => 'Logged in successfully',
                'data' => [
                    'user' => $user,
                    'access_token' => $token
                ],
            ]);
        }

        return response()->json([
            'success' => 0,
            'message' => 'Password does not match',
            'data' => []], 200);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        if (Auth::guard()->user()) {
            return response()->json([
                'success' => 1,
                'message' => 'Get data of logged in account successfully',
                'data' => ['user' => Auth::guard()->user()]]);
        }
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = Auth::guard()->user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'success' => 1,
            'message' => 'Successfully logged out',
            'data' => []], 200);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'oldPassword' => 'required',
            'newPassword' => ['required', 'different:oldPassword', 'max:20', Password::min(6)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()],
            'confirmPassword' => 'required|same:newPassword',
        ], [

        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json([
                'success' => 0,
                'message' => $validator->errors()->first(),
                'data' => [],
            ]);
        }
        $student = Auth::user();
        if (!Hash::check($request->oldPassword, $student->password)) {
            return response()->json([
                'success' => 0,
                'message' => 'Old password is incorrect',
                'data' => []
            ]);
        }
        $student->password = Hash::make($request->newPassword);
        $student->save();
        return response()->json([
            'success' => 1,
            'message' => 'Change password successfully',
            'data' => ['user' => $student],
        ], 200);
    }

    public function activeAccount(Request $request)
    {
        $code = Auth::user()->code;

        $request->validate([
            'email' => 'required|email|unique:student,email',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:student,phone',
            'password' => ['required', 'max:20', Password::min(6)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()],
            'confirmPassword' => 'required|same:password',
        ]);

        $token = Str::random(20);

        $verifyEmail = VertifyEmail::updateOrCreate(['code' => $code], [
            'email' => $request->email,
            'token' => $request->token,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'created_at' => now(),
        ]);

        if ($verifyEmail) {
            Mail::to($request->email)->send(new SendLinkVerifyEmail($token));
        }

        return response()->json([
            'success' => 1,
            'message' => 'We have e-mailed your password reset code!',
            'data' => [
                'code' => $code,
                'email' => $request->email,
            ]
        ]);

    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'token' => 'required|string|exists:vertify_email_tokens,token',
        ]);

        $verifyEmail = VertifyEmail::where('token', $request->token)->first();

        // check if it does not expired: the time is one hour
        if ($verifyEmail->created_at < now()->subMinute(5)) {
            $verifyEmail->delete();
            return response()->json([
                'success' => 0,
                'message' => 'Link is expire'], 422);
        }

        if ($verifyEmail) {
            $student = Auth::user();
            $student->email = $verifyEmail->email;
            $student->phone = $verifyEmail->phone;
            $student->password = $verifyEmail->password;
            $student->save();
            $verifyEmail->delete();
            return response()->json([
                'success' => 1,
                'message' => 'Verify email successfully',
                'data' => [
                    'user' => $student,
                ]
            ]);
        }

        return response()->json([
            'success' => 0,
            'message' => 'Verify email failed',
            'data' => []
        ]);
    }
}
