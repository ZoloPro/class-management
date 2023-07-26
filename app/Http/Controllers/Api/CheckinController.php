<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CheckinHistory;
use App\Models\Classroom;
use App\Models\WifiInfo;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Auth;

class CheckinController extends Controller
{
    public function generateCheckinToken(Request $request)
    {
        $key = env('ATTENDANCE_JWT_SECRET');
        $payload = [
            'classroomId' => $request->classroomId,
            'exp' => time() + (30),
        ];
        $token = JWT::encode($payload, $key, 'HS256');
        return response()->json([
            'success' => 1,
            'message' => 'Generate successfully',
            'data' => [
                'classroomId' => $request->classroomId,
                'token' => $token,
            ]
        ], 200);
    }

    public function checkIn(Request $request)
    {
        $key = env('ATTENDANCE_JWT_SECRET');
        $token = $request->token;
        if (!$token) {
            return response()->json([
                'success' => 0,
                'message' => 'Link has expired',
                'data' => [],
            ], 200);
        }
        $wifiInfos = WifiInfo::all();
        $wifiInfos = $wifiInfos->map(function ($wifiInfo) {
            return $wifiInfo->only(['wifiName', 'wifiBSSID']);
        })->toArray();
        $requestWifi = $request->only(['wifiName', 'wifiBSSID']);
        if (!in_array($requestWifi, $wifiInfos)) {
            return response()->json([
                'success' => 0,
                'message' => 'Invalid wifi network',
                'data' => []
            ], 200);
        }

        try {
            $decode = JWT::decode($token, new Key($key, 'HS256'));
            $classroom = Classroom::find($decode->classroomId);

        } catch (\Exception $exception) {
            return response()->json([
                'success' => 0,
                'message' => 'Token has expired',
                'data' => [],
            ], 200);
        }

        $student = Auth::user();
        $student = $classroom->students()->find($student->id);
        if (!$student) {
            return response()->json([
                'success' => 0,
                'message' => 'Students not in class',
                'data' => []
            ], 200);
        }
        $isCheckIn = $classroom->checkedInStudents()->where('studentId', $student->id)->wherePivot('date', date('Y-m-d'))->first();
        if ($isCheckIn) {
            return response()->json([
                'success' => 0,
                'message' => 'You have already checked in',
                'data' => [
                    'classroom' => [
                        'id' => $classroom->id,
                        'term' => $classroom->term,
                    ],
                    'date' => $isCheckIn->checkin->date,
                ],
            ], 200);
        }
        $date = date('Y-m-d');
        $classroom->checkedInStudents()->attach($student, ['date' => $date]);
        return response()->json([
            'success' => 1,
            'message' => 'Check in successfully',
            'data' => [
                'classroom' => [
                    'id' => $classroom->id,
                    'term' => $classroom->term,
                ],
                'date' => $date
            ]
        ], 200);
    }

    public function logCheckin(string $classroomId)
    {
        $lecturer = auth()->user();
        $checkinHistory = CheckinHistory::firstOrCreate([
            'classroomId' => $classroomId,
            'date' => date('Y-m-d'),
        ]);
        return response()->json([
            'success' => 1,
            'message' => 'Log checkin successfully',
            'data' => [
                'checkinHistory' => $checkinHistory
            ]
        ], 200);
    }

    public function getCheckinHistory(Request $request)
    {
        $classroomId = $request->classroomId;
        $from = $request->query('from');
        $to = $request->query('to');

        $classroom = Classroom::find($classroomId);
        $students = $classroom->students;
        $checkinHistories = CheckinHistory::where('classroomId', $classroomId)
            ->where('date', '>=', $from)
            ->where('date', '<=', $to)
            ->get();
//        dd($checkinHistories);
        $checkedInList = $students->map(function ($student) use ($checkinHistories) {
            return [
                'id' => $student->id,
                'code' => $student->code,
                'famMidName' => $student->famMidName,
                'name' => $student->name,
                'checkedInDate' => [
                    $checkinHistories->map(function ($checkinHistory) use ($student) {
                        return [
                            'date' => $checkinHistory->date,
                            'isChecked' => ($student->checkinClassrooms()->wherePivot('date', $checkinHistory->date)->exists()) ? true : false];
                    })
                ]
            ];
        });
        return response()->json([
            'success' => 1,
            'message' => 'Get checked in list successfully',
            'data' => [
                'checkedInList' => $checkedInList
            ]
        ], 200);
    }

}
