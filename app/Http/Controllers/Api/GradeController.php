<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function updateGrade(Request $request)
    {
        $lecturer = Auth::user();
        $classroom = $lecturer->classrooms()->find($request->classroomId);
        $gradesMap = [];
        foreach ($request->grades as $item) {
            if ($item['attendanceGrade'] !== null || $item['examGrade'] !== null) {
                $gradesMap[$item['studentId']] = [
                    'attendanceGrade' => $item['attendanceGrade'],
                    'examGrade' => $item['examGrade'],
                ];
            }
        }
        DB::enableQueryLog();
        $classroom->hasGrades()->sync($gradesMap);
        return DB::getQueryLog();
        $students = $classroom->registeredStudents;
        $data = $students->map(function ($student) use ($classroom) {
            $grade = $student->hasGrades()->find($classroom->id);
//            $studentGrade = $grade ? $grade->grade->grade : null;
            return [
                'id' => $student->id,
                'code' => $student->code,
                'famMidName' => $student->famMidName,
                'name' => $student->name,
                'gender' => $student->gender,
            ];
        });
        return response()->json([
            'success' => 1,
            'message' => 'Update grade successfully',
            'data' => [
                'grade' => $data
            ]
        ], 200);
    }
}
