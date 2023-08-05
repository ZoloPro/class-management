<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(string $classroomId)
    {
        $classroom = Classroom::find($classroomId);
        $students = $classroom->students->sortBy('name');
        $lecturer = $classroom->lecturer;
        $gradeList = $students->map(function ($student) use ($classroomId) {
            $grade = $student->hasGrades()->find($classroomId);
            return [
                'code' => $student->code,
                'famMidName' => $student->famMidName,
                'name' => $student->name,
                'attendance' => $grade ? $grade->grade->attendance : '',
                'coefficient1Exam1' => $grade ? $grade->grade->coefficient1Exam1 : '',
                'coefficient1Exam2' => $grade ? $grade->grade->coefficient1Exam2 : '',
                'coefficient1Exam3' => $grade ? $grade->grade->coefficient1Exam3 : '',
                'coefficient2Exam1' => $grade ? $grade->grade->coefficient2Exam1 : '',
                'coefficient2Exam2' => $grade ? $grade->grade->coefficient2Exam2 : '',
                'exam' => $grade ? $grade->grade->exam : '',
                'final' => $grade ? $grade->grade->final : '',
            ];
        });

        $studentQty = $gradeList->count();

        $isContainNull = $gradeList->contains(function ($item) {
            return $item['final'] == '';
        });

        if ($isContainNull) {
            $statistical = null;
        } else {
            $tmpArr = $gradeList->filter(function ($item) {
                return $item['final'] >= 8.5;
            });
            $statistical['a'] = [
                'quantity' => $tmpArr->count(),
                'percent' => $studentQty > 0 ? round($tmpArr->count() / $studentQty * 100, 2) : 0,
            ];

            $tmpArr = $gradeList->filter(function ($item) {
                return $item['final'] >= 7.0 && $item['final'] < 8.5;
            });
            $statistical['b'] = [
                'quantity' => $tmpArr->count(),
                'percent' => $studentQty > 0 ? round($tmpArr->count() / $studentQty * 100, 2) : 0,
            ];

            $tmpArr = $gradeList->filter(function ($item) {
                return $item['final'] >= 5.5 && $item['final'] < 7.0;
            });
            $statistical['c'] = [
                'quantity' => $tmpArr->count(),
                'percent' => $studentQty > 0 ? round($tmpArr->count() / $studentQty * 100, 2) : 0,
            ];

            $tmpArr = $gradeList->filter(function ($item) {
                return $item['final'] >= 4.0 && $item['final'] < 5.5;
            });
            $statistical['d'] = [
                'quantity' => $tmpArr->count(),
                'percent' => $studentQty > 0 ? round($tmpArr->count() / $studentQty * 100, 2) : 0,
            ];

            $tmpArr = $gradeList->filter(function ($item) {
                return $item['final'] < 4.0;
            });
            $statistical['f'] = [
                'quantity' => $tmpArr->count(),
                'percent' => $studentQty > 0 ? round($tmpArr->count() / $studentQty * 100, 2) : 0,
            ];
        }

        $date = [
            'date' => date('d'),
            'month' => date('m'),
            'year' => date('Y'),
        ];

        $pdf = Pdf::loadView('gradeReport.gradeReportTemplate', [
            'classroom' => $classroom,
            'lecturer' => $lecturer,
            'studentQty' => $studentQty,
            'gradeList' => $gradeList,
            'statistical' => $statistical,
            'date' => $date,
        ]);
        return $pdf->download('document.pdf');
    }
}
