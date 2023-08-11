<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Import\StudentsImport;
use App\Models\Semester;
use App\Models\Student;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $departmentId = $request->query('department');

        if ($departmentId != null) {
            $students = Student::where('departmentId', $departmentId)->get();
        } else {
            $students = Student::all();
        }

        // Return Json Response
        return response()->json([
            'success' => 1,
            'message' => 'Get data successfully',
            'data' => ['students' => $students]], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Save lecturer
            $student = Student::create($request->all());
            $student->code = '1' . str_pad($student->id, 7, '0', STR_PAD_LEFT);
            $student->password = Hash::make('tksv' . substr($student->code, -4));
            $student->save();
            // Return Json Response
            return response()->json([
                'success' => 1,
                'message' => "Student successfully saved",
                'data' => ['student' => $student]
            ], 201);
        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'success' => 0,
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'data' => []], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find lecturer
        $student = Student::where('code', $id)->first();

        // Return Json Response
        return response()->json(
            $student,
            200
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $student = Student::findOrFail($id);
            $data = $request->all();
            $student->update($data);

            return response()->json([
                'success' => 1,
                'message' => 'Update successfully',
                'data' => $student,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'data' => [],
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Student::destroy($id);
        return response()->json([
            'success' => 1,
            'message' => 'Deleted successfully',
            'data' => [],
        ], 200);
    }

    public function import(Request $request)
    {
        try {
            HeadingRowFormatter::default('none');
            Excel::import(new StudentsImport(), $request->file);
            return response()->json([
                'success' => 1,
                'message' => 'Data was imported successfully',
                'data' => [],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode(),
            ], 400
            );
        }
    }

    public function getAllClassrooms(string $id)
    {
        try {
            // All classrooms
            $classrooms = Student::where('code', $id)->first()->registeredClassrooms()->get();
            $response = [];
            foreach ($classrooms as $classroom) {
                $lecturer = $classroom->lecturer;
                $response[] = [
                    'id' => $classroom['id'],
                    'lecturer' => [
                        'code' => $lecturer['code'],
                        'fullname' => $lecturer['fullname']],
                    'term' => $classroom->term,
                ];
            }

            // Return Json Response
            return response()->json([
                'status' => 1,
                'classroom' => $response,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode(),
            ], 400);
        }
    }

    function resetAllPassword()
    {
        try {
            $students = Student::all();
            foreach ($students as $student) {
                $student->password = Hash::make('tksv' . substr($student->code, -4));
                $student->save();
            }
            return response()->json(
                ['message' => 'done'],
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                ['message' => $e->getMessage()],
                400
            );
        }
    }

    //Lấy tất cả danh sách lớp học của sinh viên đang đăng nhập
    public function getAllClassroomsByLoggedStudent()
    {
        try {
            $student = Auth::user();
            $classrooms = $student->registeredClassrooms;
            $classroomGroup = $classrooms->groupBy(function ($classroom) {
                return $classroom->semesterId;
            });
            $classroomGroup = $classroomGroup->sortByDesc(function ($classrooms, $semesterId) {
                $semester = Semester::find($semesterId);
                return $semester->startDate;
            });
            $semesterData = $classroomGroup->map(function ($classrooms, $semesterId) {
                $semester = Semester::find($semesterId);
                return [
                    'idSemester' => $semesterId,
                    'nameSemester' => $semester->semesterName,
                    'list_classroom' => $classrooms->map(function ($classroom) {
                        return [
                            'id' => $classroom->id,
                            'lecturer' => $classroom->lecturer->only(['code', 'fullname']),
                            'term' => $classroom->term,
                        ];
                    }),
                ];
            });
            return response()->json([
                'success' => 1,
                'message' => 'Get all classrooms successfully',
                'data' => array_values($semesterData->toArray()),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode(),
            ], 400);
        }
    }

    //Lấy thông tin chi tiết lớp học của sinh viên đang đăng nhập
    public function getClassroomDetail(Request $request)
    {
        try {
            $student = Auth::user();
            $classroom = $student->registeredClassrooms()->find($request->classroomId);
            if (!$classroom) {
                return response()->json([
                    'success' => 0,
                    'message' => 'classroom information not found',
                    'data' => []
                ], 200);
            }
            $grade = $student->hasGrades()->find($request->classroomId)->grade->grade ?? '';
            $students = $classroom->registeredStudents()->orderBy('code')->get();
            $students = $students->map(function ($student) {
                $student = collect($student);
                return $student->except('register_classroom');
            });

            $documents = $classroom->documents()->get();
            $documents = $documents->map(function ($document) {
                $document = collect($document);
                return $document->except('classroomId');
            });
            return response()->json([
                'success' => 1,
                'message' => 'Get classroom detail successfully',
                'data' => [
                    'classroom' => [
                        'id' => $classroom->id,
                        'lecturer' => $classroom->lecturer->only(['code', 'fullname']),
                        'term' => $classroom->term,

                    ],
                    'semester' => $classroom->semester->semesterName,
                    'grade' => $grade,
                    'studentList' => $students,
                    'documents' => $documents,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode(),
            ], 400);
        }
    }

    //Lấy tất cả điểm của sinh viên đang đăng nhập
    public function getGradesByLoggedStudent()
    {
        $student = Auth::user();
        $classrooms = $student->registeredClassrooms;

        $classroomGroup = $classrooms->groupBy(function ($classroom) {
            return $classroom->semester->id;
        });

        $classroomGroup = $classroomGroup->sortBy(function ($classrooms, $semesterId) {
            $semester = Semester::find($semesterId);
            return $semester->startDate;
        });

        $totalCumulativeGPA10 = 0;
        $totalCumulativeGPA4 = 0;
        $courseCreditsAll = 0;

        $semesterData = $classroomGroup->map(function ($classrooms, $semesterId) use (&$totalCumulativeGPA4, &$totalCumulativeGPA10, &$courseCreditsAll, $student) {
            $semester = Semester::find($semesterId);
            $courseCreditsAchieve = 0;
            $totalGPA10 = 0;
            $totalGPA4 = 0;
            $listGrade = [];

            foreach ($classrooms as $classroom) {
                $hasGrades = $classroom->hasGrades->find($student->id);
                $grades = $hasGrades ? $hasGrades->grade : null;
                $gpa4 = $grades && $grades->final !== null ? round($grades->final / 2.5) : '';
                if ($gpa4 !== '') {
                    $totalGPA4 += $gpa4 * $classroom->term->credit;
                    $totalGPA10 += $grades->final * $classroom->term->credit;
                    $courseCreditsAchieve += $classroom->term->credit;
                }

                $gpaCH = match ($gpa4) {
                    4.0 => 'A',
                    3.0 => 'B',
                    2.0 => 'C',
                    1.0 => 'D',
                    0.0 => 'F',
                    default => '',
                };

                if ($gpaCH == 'F')
                    $result = 'X';
                elseif ($gpaCH == '')
                    $result = '|';
                else
                    $result = 'Đạt';

                $listGrade[] = [
                    'termId' => $classroom->term->id,
                    'termName' => $classroom->term->termName,
                    'attendance' => $grades && $grades->attendance !== null ? $grades->attendance . '' : '',
                    'coefficient1Exam1' => $grades && $grades->coefficient1Exam1 !== null ? $grades->coefficient1Exam1 . '' : '',
                    'coefficient2Exam1' => $grades && $grades->coefficient1Exam2 !== null ? $grades->coefficient1Exam2 . '' : '',
                    'coefficient3Exam1' => $grades && $grades->coefficient1Exam3 !== null ? $grades->coefficient1Exam3 . '' : '',
                    'coefficient1Exam2' => $grades && $grades->coefficient2Exam1 !== null ? $grades->coefficient2Exam1 . '' : '',
                    'coefficient2Exam2' => $grades && $grades->coefficient2Exam2 !== null ? $grades->coefficient2Exam2 . '' : '',
                    'exam' => $grades && $grades->exam !== null ? $grades->exam . '' : '',
                    'final' => $grades && $grades->final !== null ? $grades->final . '' : '',
                    'gpa10' => $grades && $grades->final !== null ? $grades->final . '' : '',
                    'gpa4' => $gpa4 . '',
                    'gbaCH' => $gpaCH,
                    'result' => $result,
                ];
            };

            if ($courseCreditsAchieve > 0) {
                $semesterGPA10 = $totalGPA10 / $courseCreditsAchieve;
                $semesterGPA4 = $totalGPA4 / $courseCreditsAchieve;
            } else {
                $semesterGPA10 = '';
                $semesterGPA4 = '';
            }

            $totalCumulativeGPA10 += $totalGPA10;
            $totalCumulativeGPA4 += $totalGPA4;
            $courseCreditsAll += $courseCreditsAchieve;

            if ($courseCreditsAll > 0) {
                $cumulativeGPA10 = round($totalCumulativeGPA10 / $courseCreditsAll, 2);
                $cumulativeGPA4 = round($totalCumulativeGPA4 / $courseCreditsAll, 2);
            } else {
                $cumulativeGPA10 = '';
                $cumulativeGPA4 = '';
            }

            return [
                'idSemester' => $semesterId,
                'nameSemester' => $semester->semesterName,
                "semesterGPA10" => $semesterGPA10 . '' ?? '',
                "semesterGPA4" => $semesterGPA4 . '' ?? '',
                "cumulativeGPA10" => $cumulativeGPA10 . '' ?? '',
                "cumulativeGPA4" => $cumulativeGPA4 . '' ?? '',
                "courseCreditsAchieve" => $courseCreditsAchieve . '',
                "courseCreditsAll" => $courseCreditsAll . '',
                "list_grade" => $listGrade
            ];
        });
        return response()->json([
            'success' => 1,
            'message' => 'Get data successfully',
            'data' => array_values($semesterData->toArray())
        ], 200);
    }

    public function downloadExampleImportFile()
    {
        $file = storage_path("app/public/example/example-students.xlsx");
        $headers = [
            'Content-Type: application/xlsx',
        ];
        return response()->download($file, 'example-students.xlsx', $headers);
    }
}
