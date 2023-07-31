<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Checkin;
use App\Models\CheckinHistory;
use App\Models\Classroom;
use App\Models\WifiInfo;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CheckinController extends Controller
{
    public function generateCheckinToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:1,2,3',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => 'Invalid data',
                'data' => $validator->errors(),
            ], 200);
        }
        $type = $request->type;
        $key = env('ATTENDANCE_JWT_SECRET');
        $payload = [
            'classroomId' => $request->classroomId,
            'type' => $type,
            'exp' => time() + 12,
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
        try {
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
            $checkin = Checkin::create([
                'classroomId' => $classroom->id,
                'studentId' => $student->id,
                'type' => $decode->type,
                'date' => date('Y-m-d'),
            ]);
            $timeStr = date('d/m/y H:i:s');
            return response()->json([
                'success' => 1,
                'message' => `Check in successfully $classroom->term->termName at $timeStr`,
                'data' => [
                    'classroom' => [
                        'id' => $classroom->id,
                        'term' => $classroom->term,
                        'lecturer' => $classroom->lecturer->only(['code', 'fullname']),
                    ],
                    'type' => $checkin->type, // in, mid, out
                    'checkinTime' => $checkin->created_at
                ]
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => 0,
                'message' => $exception->getMessage(),
                'data' => []
            ], 200);
        }

    }

    public function logCheckin(string $classroomId)
    {
        $time = 0;
        if (date('H') > 12) {
            $time = 1;
        }
        $checkinHistory = CheckinHistory::firstOrCreate([
            'classroomId' => $classroomId,
            'date' => date('Y-m-d'),
            'time' => $time,
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
            ->orderBy('date')
            ->orderBy('time')
            ->get();
        $checkedInList = $students->map(function ($student) use ($checkinHistories) {
            $checkedIn = $checkinHistories->map(function ($checkinHistory) use ($student) {
                if ($checkinHistory->time == 0) {
                    $time = 0;
                    $checked = $student->checkins()->whereDate('created_at', $checkinHistory->date)->whereTime('created_at', '<', '12:00')->orderByDesc('created_at')->first(['type', 'created_at']);
                } else {
                    $time = 1;
                    $checked = $student->checkins()->whereDate('created_at', $checkinHistory->date)->whereTime('created_at', '>', '12:00')->orderByDesc('created_at')->first(['type', 'created_at']);
                }
                return [
                    'date' => $checkinHistory->date,
                    'time' => $time,
                    'checked' => $checked,
                ];
            });
            return [
                'id' => $student->id,
                'code' => $student->code,
                'famMidName' => $student->famMidName,
                'name' => $student->name,
                'checkedIn' => $checkedIn,
            ];
        });
        return response()->json([
            'success' => 1,
            'message' => 'Get checked in list successfully',
            'data' => [
                'checkinHistory' => [
                    'dates' => $checkinHistories->pluck('date'),
                    'checkedInList' => $checkedInList,
                ]
            ]
        ], 200);
    }

    function getCheckinHistoryByStudent()
    {
        try {
            $student = auth()->user();
            $classrooms = $student->registeredClassrooms;
            $checkinHistory = $classrooms->map(function ($classroom) use ($student) {
                return [
                    'classroom' => [
                        'id' => $classroom->id,
                        'term' => $classroom->term,
                        'lecturer' => $classroom->lecturer->only(['code', 'fullname']),
                    ],
                    'checkinDate' => $classroom->checkinHistory()->orderByDesc('date')->orderBy('time')->get()->map(function ($checkinHistory) use ($classroom, $student) {
                        $time = 0;
                        if ($checkinHistory->time == 0) {
                            $time = 0;
                            $record = $student->checkins()->where('classroomId', $classroom->id)->whereDate('created_at', $checkinHistory->date)->whereTime('created_at', '<', '12:00')->orderByDesc('created_at')->first();
                        } else {
                            $time = 1;
                            $record = $student->checkins()->where('classroomId', $classroom->id)->whereDate('created_at', $checkinHistory->date)->whereTime('created_at', '>', '12:00')->orderByDesc('created_at')->first();
                        }

                        $result = [
                            'date' => $checkinHistory->date,
                            'time' => $time,
                            'isChecked' => [
                                'type' => $record ? $record->type : 0,
                                'checkinTime' => $record ? $record->created_at : null,
                            ],
                        ];
                        return $result;
                    })
                ];
            });
            return response()->json([
                'success' => 1,
                'message' => 'Get checked in list successfully',
                'data' => [
                    'checkedInList' => $checkinHistory,
                ]
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => 0,
                'message' => $exception->getMessage(),
                'data' => []
            ], 200);
        }
    }
}
