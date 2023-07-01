<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Import\StudentsImport;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function getAllClassroom(string $id)
    {
        try {
            // All classrooms
            $classrooms = Student::where('code', $id)->first()->classrooms()->get();
            $response = [];
            foreach ($classrooms as $classroom) {
                $lecturer = $classroom->lecturer;
                $response[] = [
                    'id' => $classroom['id'],
                    'lecturer' => [
                        'id' => $lecturer['id'],
                        'fullname' => $lecturer['fullname']],
                    'module' => $classroom->module,
                ];
            }

            // Return Json Response
            return response()->json([
                'classroom' => $response,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Something went wrong!',
            ], 400
            );
        }
    }
}
