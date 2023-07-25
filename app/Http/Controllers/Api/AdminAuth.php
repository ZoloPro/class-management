<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminAuth extends Controller
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
            'code' => 'required|string|min:8|max:8|exists:admin,code',
            'password' => 'required|string'
        ], [
            'code.exists' => 'Admin code does not exist'
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return response()->json([
                'success' => 0,
                'message' => $validator->errors()->first(),
                'data' => []], 400);
        }

        $credentials = $request->only('code', 'password');
        if (Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();
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
        if (Auth::guard('adminToken')->user()) {
            return response()->json([
                'success' => 1,
                'message' => 'Get data of logged in account successfully',
                'data' => ['user' => Auth::user()]]);
        }
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            $user = Auth::guard()->user();
            $user->tokens()->delete();

            return response()->json([
                'success' => 1,
                'message' => 'Successfully logged out',
                'data' => []], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => 'Failed to log out',
                'error' => $e->getMessage(),
                'data' => []], 400);
        }

    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }
}
