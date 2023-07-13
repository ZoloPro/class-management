<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;
use App\Http\Middleware;

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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('/lecturers', Api\LecturerController::class);
Route::apiResource('/students', Api\StudentController::class)->middleware('auth:api');
Route::apiResource('/terms', Api\TermController::class);
Route::apiResource('/classroom', Api\ClassroomController::class);
Route::apiResource('/mark', Api\TermController::class);
*/
Route::prefix('/admin')->group(function () {
    Route::post('/login', [Api\StudentAuth::class, 'login']);

    Route::prefix('/import')->group(function () {
        Route::post('/lecturers', [Api\LecturerController::class, 'import']);
        Route::post('/students', [Api\StudentController::class, 'import']);
    });
    Route::get('/lecturers', [Api\LecturerController::class, 'index']);
    Route::post('/lecturers', [Api\LecturerController::class, 'store']);
    Route::put('/lecturers/{id}', [Api\LecturerController::class, 'update']);
    Route::delete('/lecturers/{id}', [Api\LecturerController::class, 'destroy']);
    Route::post('/import/lecturer', [Api\LecturerController::class, 'import']);
    Route::get('/terms', [Api\TermController::class, 'index']);
    Route::post('/terms', [Api\TermController::class, 'store']);
    Route::delete('/terms/{id}', [Api\TermController::class, 'destroy']);
    Route::get('/classrooms', [Api\ClassroomController::class, 'index']);

//        Route::get('/me', [Api\StudentAuth::class, 'me']);
//        Route::get('/classrooms', [Api\StudentController::class, 'getAllClassroomsByLoggedStudent']);
//        Route::get('/classrooms/{classroomId}', [Api\StudentController::class, 'getClassroomDetail']);
//        Route::get('/mark', [Api\StudentController::class, 'getMarksByLoggedStudent']);
});

Route::prefix('/student')->group(function () {
    Route::post('/login', [Api\StudentAuth::class, 'login']);

    Route::middleware('auth')->group(function () {
        Route::get('/logout', [Api\StudentAuth::class, 'logout']);
        Route::get('/me', [Api\StudentAuth::class, 'me']);
        Route::get('/classrooms', [Api\StudentController::class, 'getAllClassroomsByLoggedStudent']);
        Route::get('/classrooms/{classroomId}', [Api\StudentController::class, 'getClassroomDetail']);
        Route::get('/mark', [Api\StudentController::class, 'getMarksByLoggedStudent']);
    });
});

Route::prefix('/lecturer')->group(function () {
    Route::post('/login', [Api\LecturerAuth::class, 'login']);

    Route::middleware('auth:lecturerApi')->group(function () {
        Route::get('/logout', [Api\LecturerAuth::class, 'logout']);

        Route::get('/me', [Api\LecturerAuth::class, 'me']);
        Route::get('/classrooms', [Api\LecturerController::class, 'getClassroomsByLoggedLecturer']);
        Route::get('/classrooms/{classroomId}/mark', [Api\LecturerController::class, 'getMarksByClassroom']);
//        Route::get('/classrooms/{classroomId}/attendance', [Api\StudentController::class, 'getMarksByLoggedStudent']);
    });
});

//Route::get('/students/{id}/classrooms', [Api\StudentController::class, 'getAllClassrooms'])->middleware('api');
//
//Route::get('/lecturers/{id}/classrooms', [Api\LecturerController::class, 'getClassroomsByLecturer']);
//
//Route::post('/login/student', [Api\StudentAuth::class, 'login']);
//
//Route::get('/logout/student', [Api\StudentAuth::class, 'logout']);
//
//Route::post('/login/lecturer', [Api\LecturerAuth::class, 'login']);
//
//Route::post('/reset', [Api\LecturerAuth::class, 'resetAllPassword']);
//
//Route::get('/attendance/{id}', [Api\AttendanceController::class, 'generateAttendanceLink']);
//
//Route::post('/attendance', [Api\AttendanceController::class, 'attend']);
//
//Route::get('/me', [Api\StudentAuth::class, 'me'])->middleware('auth');
//
//Route::get('/classrooms/{id}/students', [Api\ClassroomController::class, 'getStudentsByClassroom']);

Route::fallback(function () {
    return response()->json([
        'message' => 'Page Not Found'], 404);
});
