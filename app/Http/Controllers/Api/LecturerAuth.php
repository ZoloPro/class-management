<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LecturerAuth extends Controller
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
            'code' => 'required|string|min:8|max:8|exists:lecturer,code',
            'password' => 'required|string'
        ], [
            'code.exists' => 'Lecturer code does not exist'
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json([
                'success' => 0,
                'message' => $validator->errors()->first(),
                'data' => []], 400);
        }

        $credentials = $request->only('code', 'password');
        if (Auth::guard('lecturer')->attempt($credentials)) {
            $user = Auth::guard('lecturer')->user();
            $user->tokens()->delete();
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
        if (Auth::user()) {
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
    public function logout()
    {
        $user = Auth::guard()->user();
        $user->tokens()->delete();

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
        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => 'Validation failed',
                'data' => $validator->errors()
            ], 400);
        }
        $lecuturer = Auth::user();
        if (!Hash::check($request->oldPassword, $lecuturer->password)) {
            return response()->json([
                'success' => 0,
                'message' => 'Old password is incorrect',
                'data' => []
            ], 400);
        }
        $lecuturer->password = Hash::make($request->newPassword);
        $lecuturer->save();
        return response()->json([
            'success' => 1,
            'message' => 'Change password successfully',
            'data' => ['user' => $lecuturer],
        ], 200);
    }
}
