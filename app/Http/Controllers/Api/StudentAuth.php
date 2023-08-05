<?php

namespace App\Http\Controllers\Api;

use App\Mail\SendLinkVerifyEmail;
use App\Models\Student;
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
            'newPassword' => ['required', 'different:oldPassword', 'max:50', Password::min(6)
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
        $student = Auth::user();

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:student,email',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|unique:student,phone',
        ], [
            'code.exists' => 'Student code does not exist'
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json([
                'success' => 0,
                'message' => $validator->errors()->first(),
                'data' => []], 200);
        }

        $student->email = $request->email;
        $student->phone = $request->phone;
        $student->save();

        return response()->json([
            'success' => 1,
            'message' => 'Update student information successfully',
            'data' => [
                'student' => $student
            ]
        ]);
    }
}
