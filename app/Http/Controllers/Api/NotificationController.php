<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Notification;
use App\Models\NotificationDetail;
use App\Models\Student;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        $student = Student::find($request->studentId);
        FCMService::send(
            $student->notiTokenn,
            [
                'title' => $request->title,
                'body' => $request->body,

            ],
            [
                'id' => $request->id,
                'type' => $request->type,
            ]
        );

    }

    public function getAllNotificationByStudent(Request $request)
    {
        $student = Auth::user();

        $notifications = NotificationDetail::where('userId', $student->id)->orderByDesc('time')->get();

        $data = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id . '',
                'type_notification' => $notification->type . '',
                'idNotification' => $notification->notificationId . '',
                'title' => $notification->title . '',
                'body' => $notification->body,
                'time' => $notification->time,
                'status' => $notification->status . '',
            ];
        });

        return response()->json([
            'success' => 1,
            'message' => 'success',
            'data' => $data,
        ], 200);
    }

    public function sendNotifyToStudentsOfClass(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'body' => 'required',
            'type' => 'required',
        ]);

        $classroom = Classroom::find($request->classroomId);

        foreach ($classroom->students as $student) {
            $notification = NotificationDetail::create([
                'title' => $request->title,
                'body' => $request->body,
                'type' => $request->type,
                'userId' => $student->id,
                'status' => 0,
                'time' => now(),
            ]);
            if ($student->notifyToken) {
                FCMService::send(
                    $student->notifyToken,
                    [
                        'title' => $request->title,
                        'body' => $request->body,
                    ],
                    [
                        'id' => $notification->id . '',
                        'type' => $request->type . '',
                        'idNotification' => '',
                        'title' => $request->title,
                        'body' => $request->body,
                    ],
                );
            }
        }
        return response()->json([
            'success' => 1,
            'message' => 'success',
        ], 200);
    }

    public function seenNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => $validator->errors()->first(),
            ], 200);
        }
        $notification = NotificationDetail::find($request->id);

        if ($notification->userId != Auth::user()->id) {
            return response()->json([
                'success' => 0,
                'message' => 'You are not allowed to do this',
            ], 200);
        }

        $notification->status = 1;
        $notification->save();
        return response()->json([
            'success' => 1,
            'message' => 'success',
            'data' => $notification->only(['id', 'title', 'body', 'type', 'status', 'time']),
        ], 200);
    }

}
