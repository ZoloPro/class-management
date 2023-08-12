<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Lecturer;
use App\Models\Notification;
use App\Models\NotificationDetail;
use App\Models\Student;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function getAllNotificationByStudent(Request $request)
    {
        $student = Auth::user();

        $notifications = NotificationDetail::where('user', 'student')->where('userId', $student->id)->orderByDesc('time')->get();

        $data = $notifications->map(function ($notificationDetail) {
            if ($notificationDetail->notificationId) {
                $notificationDetail->content = Notification::find($notificationDetail->notificationId)->content;
            }

            return [
                'id' => $notificationDetail->id . '',
                'type_notification' => $notificationDetail->type . '',
                'idNotification' => $notificationDetail->notificationId . '',
                'title' => $notificationDetail->title . '',
                'body' => $notificationDetail->body,
                'content' => $notificationDetail->content . '',
                'time' => $notificationDetail->time,
                'status' => $notificationDetail->status . '',
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
                'user' => 'student',
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

    public function countUnseenNotification(Request $request)
    {
        $student = Auth::user();
        $count = NotificationDetail::where('user', 'student')->where('userId', $student->id)->where('status', 0)->count();
        return response()->json([
            'success' => 1,
            'message' => 'You have ' . $count . ' unseen notification',
            'data' => $count,
        ], 200);
    }

    public function adminSendNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'body' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => $validator->errors()->first(),
            ], 200);
        }

        $notification = Notification::create([
            'title' => $request->title,
            'subtitle' => $request->body,
            'content' => $request->notifyContent,
            'type' => 3,
            'time' => now(),
        ]);

        $students = Student::all();
        $lecturers = Lecturer::all();

        foreach ($students as $student) {
            $notificationDetail = NotificationDetail::create([
                'title' => $request->title,
                'body' => $request->body,
                'type' => 3,
                'user' => 'student',
                'userId' => $student->id,
                'status' => 0,
                'time' => now(),
                'notificationId' => $notification->id,
            ]);
            if ($student->notifyToken) {
                FCMService::send(
                    $student->notifyToken,
                    [
                        'title' => $request->title,
                        'body' => $request->body,
                    ],
                    [
                        'id' => $notificationDetail->id . '',
                        'type' => $notificationDetail->type . '',
                        'idNotification' => $notification->id . '',
                        'title' => $request->title,
                        'body' => $request->body,
                        'content' => $notification->content . '',
                    ],
                );
            }
        }

        foreach ($lecturers as $lecturer) {
            $notificationDetail = NotificationDetail::create([
                'title' => $request->title,
                'body' => $request->body,
                'type' => 3,
                'user' => 'lecturer',
                'userId' => $lecturer->id,
                'status' => 0,
                'time' => now(),
                'notificationId' => $notification->id,
            ]);
        }

        return response()->json([
            'success' => 1,
            'message' => 'success',
        ], 200);
    }

}
