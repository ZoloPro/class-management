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
            'message' => 'We have e-mailed your password reset link!',
            'data' => [
                'code' => $request->code,
                'email' => $student->email
            ]
        ]);
    }

    public function resetPasswordWithToken(Request $request)
    {
        $data = [
            'success' => false,
            'message' => 'Đường link hết hạn, vui lòng thử lại',
        ];

        $validator = Validator::make($request->all(), [
            'token' => 'required|exists:password_resets,token',
        ]);

        if ($validator->fails()) {
            return view('resetPassword', $data);
        }

        // find the code
        $passwordReset = PasswordReset::firstWhere('token', $request->token);

        // check if it does not expired: the time is one hour
        if ($passwordReset->created_at < now()->subMinute(5)) {
            $passwordReset->delete();
            return view('resetPassword', $data);
        }

        $student = Student::firstWhere('email', $passwordReset->email);
        $student->password = Hash::make('tksv' . substr($student->code, -4));

        $student->isActived = 0;
        $student->save();

        // delete current code
        $passwordReset->delete();

        $data['success'] = true;
        $data['message'] = 'Đã khôi phục mật khẩu thành công, bạn có thể đóng tab này và quay lại ứng dụng';

        return view('resetPassword', $data);
    }
}
