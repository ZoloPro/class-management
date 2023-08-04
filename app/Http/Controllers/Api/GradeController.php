<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function getGradesByClassroom(Request $request)
    {
        $classroom = Classroom::find($request->classroomId);
        $students = $classroom->registeredStudents;
        $gradeList = $students->map(function ($student) use ($request) {
            $grade = $student->hasGrades()->find($request->classroomId);
            $studentGrade = [
                'attendance' => $grade ? $grade->grade->attendance : null,
                'coefficient1Exam1' => $grade ? $grade->grade->coefficient1Exam1 : null,
                'coefficient1Exam2' => $grade ? $grade->grade->coefficient1Exam2 : null,
                'coefficient1Exam3' => $grade ? $grade->grade->coefficient1Exam3 : null,
                'coefficient2Exam1' => $grade ? $grade->grade->coefficient2Exam1 : null,
                'coefficient2Exam2' => $grade ? $grade->grade->coefficient2Exam2 : null,
                'exam' => $grade ? $grade->grade->exam : null,
                'final' => $grade ? $grade->grade->final : null,
            ];
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
            'message' => 'Get data successfully',
            'data' => ['gradeList' => $gradeList],
        ], 200);
    }

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
