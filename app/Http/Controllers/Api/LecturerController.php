<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Import\lecturersImport;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class LecturerController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // All lecturers
        $lecturers = Lecturer::all();

        // Return Json Response
        return response()->json([
            'lecturers' => $lecturers
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Save lecturer
            $lecturer = Lecturer::create($request->all());
            $lecturer->code = '1' . str_pad($lecturer->id, 7, '0', STR_PAD_LEFT);
            $lecturer->save();
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
        $lecturer = Lecturer::where('code', $id)->first();

        // Return Json Response
        return response()->json(
            $lecturer,
            200);
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

    /**
     * Import data from Exel file.
     */
    public function import(Request $request)
    {
        try {
            HeadingRowFormatter::default('none');
            Excel::import(new lecturersImport, $request->file);
            return response()->json([
                'message' => 'Data was imported successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong!'
            ], 400);
        }
    }

}
