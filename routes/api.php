<?php

use App\Http\Middleware\EnsureClassroomOwner;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('/admin')->group(function () {
    Route::post('/login', [Api\AdminAuth::class, 'login']);


    Route::middleware('auth:adminToken')->group(function () {
        Route::prefix('/import')->group(function () {
            Route::post('/lecturers', [Api\LecturerController::class, 'import']);
            Route::get('/lecturers/example', [Api\LecturerController::class, 'downloadExampleImportFile']);
            Route::post('/students', [Api\StudentController::class, 'import']);
            Route::get('/students/example', [Api\StudentController::class, 'downloadExampleImportFile']);
            Route::post('/terms', [Api\TermController::class, 'import']);
            Route::get('/terms/example', [Api\TermController::class, 'downloadExampleImportFile']);
        });

        Route::get('/logout', [Api\AdminAuth::class, 'logout']);

        Route::get('/lecturers', [Api\LecturerController::class, 'index']);
        Route::post('/lecturers', [Api\LecturerController::class, 'store']);
        Route::put('/lecturers/{id}', [Api\LecturerController::class, 'update']);
        Route::delete('/lecturers/{id}', [Api\LecturerController::class, 'destroy']);

        Route::get('/students', [Api\StudentController::class, 'index']);
        Route::post('/students', [Api\StudentController::class, 'store']);
        Route::put('/students/{id}', [Api\StudentController::class, 'update']);
        Route::delete('/students/{id}', [Api\StudentController::class, 'destroy']);

        Route::get('/terms', [Api\TermController::class, 'index']);
        Route::post('/terms', [Api\TermController::class, 'store']);
        Route::put('/terms/{id}', [Api\TermController::class, 'update']);
        Route::delete('/terms/{id}', [Api\TermController::class, 'destroy']);

        Route::get('/classrooms', [Api\ClassroomController::class, 'index']);
        Route::post('/classrooms', [Api\ClassroomController::class, 'store']);
        Route::delete('/classrooms/{id}', [Api\ClassroomController::class, 'destroy']);
        Route::get('/classrooms/{id}', [Api\ClassroomController::class, 'getStudentsByClassroom']);
        Route::put('/classrooms/{id}/student', [Api\ClassroomController::class, 'updateStudentListByClassroom']);
    });
});

Route::prefix('/student')->group(function () {
    Route::post('/login', [Api\StudentAuth::class, 'login']);

    Route::middleware('auth:studentToken')->group(function () {
        Route::get('/logout', [Api\StudentAuth::class, 'logout']);
        Route::get('/me', [Api\StudentAuth::class, 'me']);
        Route::get('/classrooms', [Api\StudentController::class, 'getAllClassroomsByLoggedStudent']);
        Route::post('/detail/', [Api\StudentController::class, 'getClassroomDetail']);
        Route::get('/grade', [Api\StudentController::class, 'getGradesByLoggedStudent']);
        Route::post('/password', [Api\StudentAuth::class, 'changePassword']);
        Route::post('/checkin', [Api\AttendanceController::class, 'checkIn']);

    });
});

Route::prefix('/lecturer')->group(function () {
    Route::post('/login', [Api\LecturerAuth::class, 'login']);

    Route::middleware('auth:lecturerToken')->group(function () {
        Route::get('/logout', [Api\LecturerAuth::class, 'logout']);

        Route::get('/me', [Api\LecturerAuth::class, 'me']);
        Route::get('/classrooms', [Api\LecturerController::class, 'getClassroomsByLoggedLecturer']);
        Route::get('/classrooms/{classroomId}/mark', [Api\LecturerController::class, 'getMarksByClassroom']);
//        Route::get('/classrooms/{classroomId}/attendance', [Api\StudentController::class, 'getMarksByLoggedStudent']);

        Route::get('/documents/{classroomId}', [Api\DocumentController::class, 'getDocumentsByClassLecturer']);
        Route::post('/documents/{classroomId}', [Api\DocumentController::class, 'uploadFile']);
        Route::delete('/documents/{classroomId}', [Api\DocumentController::class, 'destroy']);

        Route::get('/attendance/{classroomId}', [Api\AttendanceController::class, 'generateAttendanceLink'])->middleware(EnsureClassroomOwner::class);

        Route::post('/password', [Api\LecturerAuth::class, 'changePassword']);
    });
});

//
//Route::post('/attendance', [Api\AttendanceController::class, 'attend']);

Route::fallback(function () {
    return response()->json([
        'message' => 'Page Not Found'], 404);
});
