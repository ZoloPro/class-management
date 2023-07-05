<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use Complex\Exception;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use function PHPUnit\Framework\throwException;

class AttendanceController extends Controller
{
    public function generateAttendanceLink(Request $request)
    {
        $key = env('ATTENDANCE_JWT_SECRET');
        $payload = [
            'classroomId' => $request->id,
            'exp' => time() + 30,
        ];
        $token = JWT::encode($payload, $key, 'HS256');
        return response()->json([
            'status' => 1,
            'URL' => "{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}/api/attendance?token={$token}",
        ], 200);
    }

    public function attend(Request $request)
    {
        try {
            $key = env('ATTENDANCE_JWT_SECRET');
            $token = $request->query('token');
            if (!$token) {
                return response()->json([
                    'status' => 0,
                    'message' => 'token is unavailable'
                ], 400);
            }
            $decode = JWT::decode($token, new Key($key, 'HS256'));
            $classroom = Classroom::find($decode->id);
            $student = $classroom->registeredClassrooms()->where('code', $request->studentCode);
            if(!$student) {
                throwException(new Exception('No student information found in the classroom'));
            }
            $classroom->attendedClassrooms()->attach($student, ['date' => date('Y-m-d')]);
            return response()->json([
                'status' => 1,
                'message' => 'Attended successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
