<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Import\StudentsImport;
use App\Models\Classroom;
use App\Models\Semester;
use App\Models\Student;
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
    public function index()
    {
        // All students
        $studens = Student::all();

        // Return Json Response
        return response()->json([
            'success' => 1,
            'message' => 'Get data successfully',
            'data' => ['students' => $studens]], 200);
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
                'message' => "Lecturer successfully saved",
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
                $semster = Semester::find($semesterId);
                return $semster->startDate;
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
        try {
            $student = Auth::user();
            $classrooms = $student->registeredClassrooms;
            $grades = $classrooms->groupBy(function ($classroom) {
                return $classroom->semester;
            });
            dd($grades);
        } catch (\Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode(),
            ], 400);
        }
    }

    public function getGradesOfClassroom(Request $request)
    {
        $classroomId = $request->classroomId;
        $classroom = Classroom::find($classroomId);
        $examGradeList = $classroom->hasGrades;
        $examGradeList = $examGradeList->map(function ($grade) {
            return $grade->grade->exam;
        });

        return response()->json([
            'success' => 1,
            'message' => 'Get data successfully',
            'data' => [
                'examGradeList' => $examGradeList
            ]
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
