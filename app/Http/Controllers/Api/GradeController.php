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
        foreach ($request->gradeList as $item) {
            $gradesMap[$item['id']] = $item['grade'];
        }
        $classroom->hasGrades()->sync($gradesMap);
        $students = $classroom->registeredStudents;
        $data = $students->map(function ($student) use ($classroom) {
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
