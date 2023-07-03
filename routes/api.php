<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('/lecturers', Api\LecturerController::class);
Route::apiResource('/students', Api\StudentController::class);
Route::apiResource('/modules', Api\ModuleController::class);
Route::apiResource('/classroom', Api\ClassroomController::class);
Route::apiResource('/mark', Api\ModuleController::class);

Route::prefix('/import')->group(function () {
    Route::post('/lecturers', [Api\LecturerController::class, 'import']);
    Route::post('/students', [Api\StudentController::class, 'import']);
});

Route::get('/students/{id}/classrooms', [Api\StudentController::class, 'getAllClassrooms']);


Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found'], 404);
});
