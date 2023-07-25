<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\TestFixture\func;

class GradeController extends Controller
{
    public function undateGrade(Request $request)
    {

        $lecturer = Auth::user();
        $classroom = $lecturer->classrooms()->find($request->classroomId);
        $grades = $request->grades->map(function ($grade) {
            return [
                $grade['studentId'] => $grade['grade'],
            ];
        });
        $classroom->hasGrades()->sync($grades);
        $studens = $classroom->registeredStudents;
        $data = $studens->map(function ($student) use ($classroom) {
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
