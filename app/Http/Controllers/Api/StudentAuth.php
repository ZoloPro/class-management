<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
            'code' => 'required|string|min:8|max:8|exists:student,code',
            'password' => 'required|string'
        ], [
            'code.exists' => 'Student code does not exist'
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json([
                'success' => 0,
                'message' => $validator->errors()->first(),
                'data' => []], 400);
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
            'data' => []], 401);
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
            'newPassword' => 'required',
            'confirmPassword' => 'required|same:newPassword',
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
}
