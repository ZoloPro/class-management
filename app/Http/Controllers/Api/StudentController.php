<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Import\StudentsImport;
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
            'students' => $studens
        ], 200);
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
            $student->password = Hash::make(substr($student->code, -4));
            $student->save();
            // Return Json Response
            return response()->json([
                'message' => "Lecturer successfully saved."
            ], 201);
        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function import(Request $request)
    {
        try {
            HeadingRowFormatter::default('none');
            Excel::import(new StudentsImport(), $request->file);
            return response()->json([
                'message' => 'Data was imported successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong!'
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
                    'module' => $classroom->module,
                ];
            }

            // Return Json Response
            return response()->json([
                'status' => 1,
                'data' => ['classroom' => $response],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'error' => $e->getMessage(),
                'message' => 'Something went wrong!',
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
            $classrooms = $student->registeredClassrooms()->get();
            $response = [];
            foreach ($classrooms as $classroom) {
                $lecturer = $classroom->lecturer()->get(['code', 'fullname']);
                $response[] = [
                    'id' => $classroom['id'],
                    'lecturer' => $lecturer,
                    'module' => $classroom->module,
                ];
            }
            return response()->json([
                'status' => 1,
                'message' => 'Get data successfully',
                'data' => ['classrooms' => $response],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'error' => $e->getMessage(),
                'message' => 'Something went wrong!',
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
                    'status' => 0,
                    'message' => 'classroom information not found!',
                ], 400);
            }
            $mark = $student->hasMarks()->find($request->classroomId)->mark->mark;
            $students = $classroom->registeredStudents()->get();
            $students = $students->map(function ($student) {
                return $student->only(['code', 'famMidName', 'name', 'gender']);
            });
            return response()->json([
                'status' => 1,
                'message' => 'Get data successfully',
                'data' => [
                    'id' => $classroom->id,
                    'lecturer' => $classroom->lecturer->only(['code', 'fullname']),
                    'module' => $classroom->module,
                    'mark' => $mark,
                    'students' => $students,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'error' => $e->getMessage(),
                'message' => 'Something went wrong!',
            ], 400);
        }
    }

    //Lấy tất cả điểm của sinh viên đăng đăng nhập
    public function getMarksByLoggedStudent()
    {
        try {
            $student = Auth::user();
            $marks = $student->hasMarks;
            $marks = $marks->map(function ($mark) {
                return [
                    'moduleId' => $mark->module->id,
                    'moduleName' => $mark->module->moduleName,
                    'mark' => $mark->mark->mark

                ];
            });
            return response()->json([
                'status' => 1,
                'message' => 'Get data successfully',
                'data' => ['marks' => $marks],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'error' => $e->getMessage(),
                'message' => 'Something went wrong!',
            ], 400);
        }
    }
}
