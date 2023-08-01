<?php

use App\Http\Middleware\EnsureClassroomOwner;
use App\Http\Middleware\EnsureInClassroom;
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
        Route::put('/classrooms/{id}', [Api\ClassroomController::class, 'update']);
        Route::get('/classrooms/{id}', [Api\ClassroomController::class, 'getStudentsByClassroom']);
        Route::put('/classrooms/{id}/student', [Api\ClassroomController::class, 'updateStudentListByClassroom']);

        Route::get('/wifi-info', [Api\WifiInfoController::class, 'index']);
        Route::post('/wifi-info', [Api\WifiInfoController::class, 'store']);
        Route::delete('/wifi-info/{wifiInfoId}', [Api\WifiInfoController::class, 'destroy']);
        Route::put('/wifi-info/{wifiInfoId}', [Api\WifiInfoController::class, 'edit']);
    });
});

Route::prefix('/student')->group(function () {
    Route::post('/login', [Api\StudentAuth::class, 'login']);

    Route::middleware('auth:studentToken')->group(function () {
        Route::get('/logout', [Api\StudentAuth::class, 'logout']);
        Route::get('/me', [Api\StudentAuth::class, 'me']);
        Route::get('/classrooms', [Api\StudentController::class, 'getAllClassroomsByLoggedStudent']);
        Route::post('/detail', [Api\StudentController::class, 'getClassroomDetail']);
        Route::get('/grade', [Api\StudentController::class, 'getGradesByLoggedStudent']);
        Route::post('/grade-list', [Api\StudentController::class, 'getGradesOfClassroom'])->middleware(EnsureInClassroom::class);
        Route::post('/password', [Api\StudentAuth::class, 'changePassword']);
        Route::post('/checkin', [Api\CheckinController::class, 'checkIn']);
        Route::get('/checkin-history', [Api\CheckinController::class, 'getCheckinHistoryByStudent']);

    });
});

Route::prefix('/lecturer')->group(function () {
    Route::post('/login', [Api\LecturerAuth::class, 'login']);

    Route::middleware('auth:lecturerToken')->group(function () {
        Route::get('/logout', [Api\LecturerAuth::class, 'logout']);

        Route::get('/me', [Api\LecturerAuth::class, 'me']);
        Route::get('/classrooms', [Api\LecturerController::class, 'getClassroomsByLoggedLecturer']);

        Route::get('/documents/{classroomId}', [Api\DocumentController::class, 'getDocumentsByClassLecturer']);
        Route::post('/documents/{classroomId}', [Api\DocumentController::class, 'uploadFile']);
        Route::delete('/documents/{classroomId}', [Api\DocumentController::class, 'destroy']);

        Route::get('/checkin/{classroomId}', [Api\CheckinController::class, 'generateCheckinToken'])->middleware(EnsureClassroomOwner::class);
        Route::post('/checkin/{classroomId}', [Api\CheckinController::class, 'logCheckin'])->middleware(EnsureClassroomOwner::class);
        Route::get('/checkin/{classroomId}/history', [Api\CheckinController::class, 'getCheckinHistory'])->middleware(EnsureClassroomOwner::class);

        Route::post('/password', [Api\LecturerAuth::class, 'changePassword']);

        Route::get('/grades/{classroomId}', [Api\LecturerController::class, 'getGradesByClassroom'])->middleware(EnsureClassroomOwner::class);
        Route::put('/grades/{classroomId}', [Api\GradeController::class, 'updateGrade'])->middleware(EnsureClassroomOwner::class);
    });
});

//
//Route::post('/attendance', [Api\CheckinController::class, 'attend']);

Route::fallback(function () {
    return response()->json([
        'message' => 'Page Not Found'], 404);
});
