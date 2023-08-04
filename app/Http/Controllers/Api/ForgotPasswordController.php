<?php

namespace App\Http\Controllers\Api;

use App\Helpers\EmailHelpers;
use App\Http\Controllers\Controller;
use App\Mail\SendCodeResetPassword;
use App\Models\PasswordReset;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'code' => 'required|exists:student,code',
            'email' => 'required|exists:student,email,code,' . $request->code,
        ]);

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
            'message' => 'We have e-mailed your password reset code!',
            'data' => [
                'code' => $request->code,
                'email' => $student->email
            ]
        ]);
    }

    public function resetPasswordWithToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string|exists:password_reset_tokens',
        ], [
            'token.exists' => 'The token is invalid.'
        ]);

        // find the code
        $passwordReset = PasswordReset::firstWhere('token', $request->token);

        // check if it does not expired: the time is one hour
        if ($passwordReset->created_at < now()->subMinute(5)) {
            $passwordReset->delete();
            return response()->json([
                'success' => 0,
                'message' => 'OTP is expire'], 422);
        }

        $student = Student::firstWhere('email', $passwordReset->email);
        $student->password = Hash::make('tksv' . substr($student->code, -4));

        $student->save();

        // delete current code
        $passwordReset->delete();

        return response()->json([
            'success' => 1,
            'message' => 'password has been successfully reset'], 200);
    }

}
