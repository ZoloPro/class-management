<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SemesterController extends Controller
{
    public function index()
    {
        $semesters = Semester::all();

        return response()->json([
            'success' => 1,
            'message' => 'Get all semesters successfully!',
            'data' => ['semesters' => $semesters]
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'semesterName' => 'required|unique:semester,semesterName',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $lastDate = Semester::orderBy('endDate', 'desc')->first();

        if ($lastDate) {
            if ($request->startDate < $lastDate->endDate) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Start date must be after end date of last semester!',
                ], 400);
            }
        }

        $semester = Semester::create($request->all());

        return response()->json([
            'success' => 1,
            'message' => 'Create semester successfully!',
            'data' => ['semester' => $semester]
        ], 201);
    }

    public function destroy(string $semesterId)
    {
        $semester = Semester::find($semesterId);

        $semester->delete();

        return response()->json([
            'success' => 1,
            'message' => 'Delete semester successfully!',
        ], 200);
    }

    public function update(string $semesterId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'semesterName' => 'required|unique:semester,semesterName',
            /*'startDate' => 'required|date',
            'endDate' => 'required|date|after:start_date',*/
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $semester = Semester::find($semesterId);
        $semester->semesterName = $request->semesterName;
        $semester->save();

        return response()->json([
            'success' => 1,
            'message' => 'Update semester successfully!',
            'data' => ['semester' => $semester]
        ], 200);
    }
}
