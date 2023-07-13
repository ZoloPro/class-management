<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class StudentAuth extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

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
        if ($token = $this->guard()->attempt($credentials)) {
            return $this->respondWithToken($token);
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
        if ($this->guard()->user()) {
            return response()->json([
                'success' => 1,
                'message' => 'Get data of logged in account successfully',
                'data' => $this->guard()->user()]);
        }
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        return response()->json([
            'success' => 1,
            'message' => 'Successfully logged out',
            'data' => []], 200);
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

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'success' => 1,
            'message' => 'Logged in successfully',
            'data' => [
                'user' => $this->guard()->user(),
                'access_token' => $token
            ],
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }
}
