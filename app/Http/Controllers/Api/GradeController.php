<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function updateGrade(Request $request)
    {

        $lecturer = Auth::user();
        $classroom = $lecturer->classrooms()->find($request->classroomId);
        $gradesMap = [];
        foreach ($request->grades as $item) {
            if ($item['grade'] !== null) {
                $gradesMap[$item['studentId']] = ['grade' => $item['grade']];
            }
        }
        $classroom->hasGrades()->sync($gradesMap);
        $students = $classroom->registeredStudents;
        $data = $students->map(function ($student) use ($classroom) {
            $grade = $student->hasGrades()->find($classroom->id);
            $studentGrade = $grade ? $grade->grade->grade : null;
            return [
                'id' => $student->id,
                'code' => $student->code,
                'famMidName' => $student->famMidName,
                'name' => $student->name,
                'gender' => $student->gender,
                'grade' => $studentGrade,
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
