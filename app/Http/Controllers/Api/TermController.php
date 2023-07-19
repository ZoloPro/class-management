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
            'success' => 0,
            'message' => 'Get data successfully',
            'data' => ['terms' => $terms]], 200);
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
                'success' => 0,
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode(),
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
        try {
            // Update term
            $term = Term::find($id)->update($request->all());

            // Return Json Response
            return response()->json([
                'success' => 1,
                'message' => "Term successfully updated",
                'data' => ['term' => $term]
            ], 200);
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Term::destroy($id);
            return response()->json([
                'success' => 1,
                'message' => 'Deleted successfully',
                'data ' => []
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode(),
            ], 400);
        }

    }
}
