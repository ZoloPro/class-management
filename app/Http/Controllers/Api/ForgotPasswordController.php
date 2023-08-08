<?php

namespace App\Http\Controllers\Api;

use App\Helpers\EmailHelpers;
use App\Http\Controllers\Controller;
use App\Mail\SendCodeResetPassword;
use App\Models\PasswordReset;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class ForgotPasswordController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|exists:student,code',
            'email' => 'required|exists:student,email,code,' . $request->code,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => $validator->errors()->first(),
            ]);
        }

        $student = Student::where('code', $request->code)->first();

        $token = Str::random(20);

        $passwordReset = PasswordReset::updateOrCreate(['email' => $student->email], [
            'token' => $token,
            'created_at' => now()]);

        if ($passwordReset) {
            Mail::to($student->email)->send(new SendCodeResetPassword($token));
        }

        return response()->json([
            'success' => 1,
            'message' => 'We have e-mailed your password reset link!',
            'data' => [
                'code' => $request->code,
                'email' => $student->email
            ]
        ]);
    }

    public function showForgotPasswordForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|exists:password_reset_tokens,token',
        ]);
        if ($validator->fails()) {
            return view('resetPassword')->withErrors([
                'resetPassword' => 'Link đã hết hạn'
            ]);
        }
        return view('forgotPassword', [
            'token' => $request->token,
        ]);
    }

    public function resetPasswordWithToken(Request $request)
    {
        $checkToken = Validator::make($request->all(), [
            'token' => 'required|exists:password_reset_tokens,token',
        ]);

        if ($checkToken->fails()) {
            return view('resetPassword')->withErrors([
                'resetPassword' => 'Link đã hết hạn'
            ]);
        }

        $passwordReset = PasswordReset::where('token', $request->token)->first();

        if ($passwordReset->created_at < now()->subMinute(5)) {
            $passwordReset->delete();
            return view('resetPassword')->withErrors([
                'resetPassword' => 'Link đã hết hạn',
            ]);
        }

        $checkPassword = Validator::make($request->all(), [
            'password' => ['required', 'confirmed', Password::min(6)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()],
        ]);

        if ($checkPassword->fails()) {
            return view('resetPassword')->withErrors([
                'resetPassword' => $checkPassword->errors()->first(),
            ]);
        }

        $student = Student::where('email', $passwordReset->email)->first();

        if (!$student) {
            $passwordReset->delete();
            return view('resetPassword')->withErrors([
                'resetPassword' => 'Link đã hết hạn',
            ]);
        }

        $student->password = Hash::make($request->password);
        $student->save();

        $passwordReset->delete();

        return view('resetPassword', [
            'message' => 'Bạn đã khôi phục mật khẩu thành công'
        ]);

    }


}
