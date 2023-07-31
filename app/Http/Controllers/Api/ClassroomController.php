<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // All classrooms
        $classrooms = Classroom::all();
        $classrooms = $classrooms->map(function ($classroom) {
            return [
                'id' => $classroom->id,
                'term' => $classroom->term,
                'lecturer' => $classroom->lecturer,
                'startDate' => $classroom->startDate,
                'endDate' => $classroom->endDate,
            ];
        });
        // Return Json Response
        return response()->json([
            'success' => 1,
            'message' => 'Get data successfully',
            'data' => ['classrooms' => $classrooms]], 200);
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
            // Save classroom
            $classroom = Classroom::create($request->all());

            // Return Json Response
            return response()->json([
                'success' => 1,
                'message' => "Classroom created successfully.",
                'data' => ['classroom' => $classroom],
            ], 201);
        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'success' => 0,
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode(),
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        try {
            Classroom::destroy($id);
            return response()->json([
                'success' => 1,
                'message' => "Deleted classroom with id {$id} successfully",
                'data' => []], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode(),
            ], 400
            );
        }
    }

    public function getStudentsByClassroom(string $id)
    {
        try {
            $classroom = Classroom::find($id);
            if (!$classroom) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Classroom information not found',
                    'data' => []
                ], 400);
            }
            $lecturer = $classroom->lecturer->only(['code', 'fullname']);
            $students = $classroom->registeredStudents()->get();
            $students = $students->map(function ($student) {
                return $student->only(['id', 'code', 'famMidName', 'name', 'gender']);
            });

            // Return Json Response
            return response()->json([
                'success' => 1,
                'message' => 'Get data successfully',
                'data' => ['classroom' => [
                    'lecture' => $lecturer,
                    'term' => $classroom->term,
                    'students' => $students
                ]]], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode(),
            ], 400);
        }
    }

    function updateStudentListByClassroom(string $id, Request $request)
    {
        $classroom = Classroom::find($id);
        if (!$classroom) {
            return response()->json([
                'success' => 0,
                'message' => 'Classroom information not found',
                'data' => []
            ], 400);
        }
        $requestStudents = $request->students;
        foreach ($requestStudents as $requestStudent) {
            $student = Student::find($requestStudent);
            $hasSampTerm = $student->registeredClassrooms()->where('termId', $classroom->termId)->exists();
            if ($hasSampTerm) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Student ' . $student->code . ' has already registered this term',
                    'data' => []
                ], 400);
            }
        }
        $classroom->registeredStudents()->sync($request->students);
        $lecturer = $classroom->lecturer->only(['code', 'fullname']);
        $students = $classroom->registeredStudents()->get();
        $students = $students->map(function ($student) {
            return $student->only(['id', 'code', 'famMidName', 'name', 'gender']);
        });
        return response()->json([
            'success' => 1,
            'message' => 'Update student list successfully',
            'data' => ['classroom' => [
                'lecture' => $lecturer,
                'term' => $classroom->term,
                'students' => $students
            ]]], 200);
    }
}
