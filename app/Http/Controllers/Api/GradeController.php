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

    public function gradeList(Request $request)
    {
        $classroomId = $request->classroomId;
        $classroom = Classroom::find($classroomId);
        $gradeList = $classroom->hasGrades;

        $examGradeList = $gradeList->map(function ($item) {
            return $item->grade->exam;
        });
        //Loại bỏ những phần tử null
        $examGradeList = $examGradeList->filter(function ($item) {
            return $item != null;
        });
        $totalAmountExam = $examGradeList->count();
        if ($totalAmountExam < 1) {
            return response()->json([
                'success' => 0,
                'message' => 'Lớp chưa thi tổng kết cuối kì',
                'data' => [],
            ], 200);
        }
        $exam = [];

        $tmpArr = $examGradeList->filter(function ($item) {
            return $item < 4;
        });
        $tmpArrCount = $tmpArr->count();
        $percentage = round($tmpArrCount / $totalAmountExam * 100, 2);
        $statistical = [
            'percentages' => $percentage . '',
            'nameAmountPercentages' => "$tmpArrCount - $percentage%",
            'name' => 'Tỉ lệ điểm dưới 4.0',
            'subname' => '0<4',
        ];
        $exam[] = $statistical;

        $tmpArr = $examGradeList->filter(function ($item) {
            return $item >= 4 && $item < 5.5;
        });
        $tmpArrCount = $tmpArr->count();
        $percentage = round($tmpArrCount / $totalAmountExam * 100, 2);
        $statistical = [
            'percentages' => $percentage . '',
            'nameAmountPercentages' => "$tmpArrCount - $percentage%",
            'name' => 'Tỉ lệ điểm từ 4.0 đến dưới 5.5.',
            'subname' => '4>= & <5.5',
        ];
        $exam[] = $statistical;

        $tmpArr = $examGradeList->filter(function ($item) {
            return $item >= 5.5 && $item < 7.0;
        });
        $tmpArrCount = $tmpArr->count();
        $percentage = round($tmpArrCount / $totalAmountExam * 100, 2);
        $statistical = [
            'percentages' => $percentage . '',
            'nameAmountPercentages' => "$tmpArrCount - $percentage%",
            'name' => 'Tỉ lệ điểm từ 5.5 tới dưới 7.0.',
            'subname' => '5.5>= & <7.0',
        ];
        $exam[] = $statistical;

        $tmpArr = $examGradeList->filter(function ($item) {
            return $item >= 7.0 && $item < 8.5;
        });
        $tmpArrCount = $tmpArr->count();
        $percentage = round($tmpArrCount / $totalAmountExam * 100, 2);
        $statistical = [
            'percentages' => $percentage . '',
            'nameAmountPercentages' => "$tmpArrCount - $percentage%",
            'name' => 'Tỉ lệ điểm từ 7.0 tới dưới 8.5.',
            'subname' => '7.0>= & <8.5',
        ];
        $exam[] = $statistical;

        $tmpArr = $examGradeList->filter(function ($item) {
            return $item >= 8.5;
        });
        $tmpArrCount = $tmpArr->count();
        $percentage = round($tmpArrCount / $totalAmountExam * 100, 2);
        $statistical = [
            'percentages' => $percentage . '',
            'nameAmountPercentages' => "$tmpArrCount - $percentage%",
            'name' => 'Tỉ lệ điểm từ 8.5 trở lên.',
            'subname' => '8.5>= & <=10'
        ];
        $exam[] = $statistical;

        // Thống kê final
        $finalGradeList = $gradeList->map(function ($item) {
            return $item->grade->final;
        });
        //Loại bỏ những phần tử null
        $finalGradeList = $finalGradeList->filter(function ($item) {
            return $item != null;
        });
        $totalAmountFinal = $finalGradeList->count();
        if ($totalAmountExam < 1) {
            return response()->json([
                'success' => 0,
                'message' => 'Lớp không có điểm trung bình cuối kì',
                'data' => [],
            ], 200);
        }
        $final = [];

        $tmpArr = $finalGradeList->filter(function ($item) {
            return $item < 4;
        });
        $tmpArrCount = $tmpArr->count();
        $percentage = round($tmpArrCount / $totalAmountFinal * 100, 2);
        $statistical = [
            'percentages' => $percentage . '',
            'nameAmountPercentages' => "$tmpArrCount - $percentage%",
            'name' => 'Tỉ lệ điểm dưới 4.0',
            'subname' => '0<4',
        ];
        $final[] = $statistical;

        $tmpArr = $finalGradeList->filter(function ($item) {
            return $item >= 4 && $item < 5.5;
        });
        $tmpArrCount = $tmpArr->count();
        $percentage = round($tmpArrCount / $totalAmountFinal * 100, 2);
        $statistical = [
            'percentages' => $percentage . '',
            'nameAmountPercentages' => "$tmpArrCount - $percentage%",
            'name' => 'Tỉ lệ điểm từ 4.0 đến dưới 5.5.',
            'subname' => '4>= & <5.5',
        ];
        $final[] = $statistical;

        $tmpArr = $finalGradeList->filter(function ($item) {
            return $item >= 5.5 && $item < 7.0;
        });
        $tmpArrCount = $tmpArr->count();
        $percentage = round($tmpArrCount / $totalAmountFinal * 100, 2);
        $statistical = [
            'percentages' => $percentage . '',
            'nameAmountPercentages' => "$tmpArrCount - $percentage%",
            'name' => 'Tỉ lệ điểm từ 5.5 tới dưới 7.0.',
            'subname' => '5.5>= & <7.0',
        ];
        $final[] = $statistical;

        $tmpArr = $finalGradeList->filter(function ($item) {
            return $item >= 7.0 && $item < 8.5;
        });
        $tmpArrCount = $tmpArr->count();
        $percentage = round($tmpArrCount / $totalAmountFinal * 100, 2);
        $statistical = [
            'percentages' => $percentage . '',
            'nameAmountPercentages' => "$tmpArrCount - $percentage%",
            'name' => 'Tỉ lệ điểm từ 7.0 tới dưới 8.5.',
            'subname' => '7.0>= & <8.5',
        ];
        $final[] = $statistical;

        $tmpArr = $finalGradeList->filter(function ($item) {
            return $item >= 8.5;
        });
        $tmpArrCount = $tmpArr->count();
        $percentage = round($tmpArrCount / $totalAmountFinal * 100, 2);
        $statistical = [
            'percentages' => $percentage . '',
            'nameAmountPercentages' => "$tmpArrCount - $percentage%",
            'name' => 'Tỉ lệ điểm từ 8.5 trở lên.',
            'subname' => '8.5>= & <=10'
        ];
        $final[] = $statistical;

        return response()->json([
            'success' => 1,
            'message' => 'Get data successfully',
            'data' => [
                'exam' => $exam,
                'final' => $final,
            ],
        ], 200);

    }

}
