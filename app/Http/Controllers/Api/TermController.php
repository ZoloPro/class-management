<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\Http\Request;

class TermController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // All terms
        $terms = Term::all();

        // Return Json Response
        return response()->json([
            'terms' => $terms
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
            // Save term
            Term::create($request->all());

            // Return Json Response
            return response()->json([
                'message' => "Term successfully saved."
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
            Term::destroy($id);
            return response()->json([
                'status' => 0,
                'message' => 'Deleted successfully',
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