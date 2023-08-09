<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class departmentController extends Controller
{
    public function index()
    {
        $departments = Department::all();

        return response()->json([
            'success' => 1,
            'message' => 'Get all departments successfully',
            'data' => [
                'departments' => $departments]
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'departmentName' => 'required|unique:department',
        ]);

        $department = Department::create([
            'departmentName' => $request->departmentName,
        ]);

        return response()->json([
            'success' => 1,
            'message' => 'Department created successfully',
            'data' => $department
        ], 200);
    }

    public function delete(string $departmentId)
    {
        $department = Department::find($departmentId);

        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data deleted successfully',
        ], 200);
    }

    public function update(Request $request, string $departmentId)
    {
        $request->validate([
            'departmentName' => 'required|unique:department',
        ]);

        $department = Department::find($departmentId);

        $department->update([
            'departmentName' => $request->departmentName,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data updated successfully',
            'data' => $department
        ], 200);
    }
}
