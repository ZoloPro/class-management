<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WifiInfo;
use Illuminate\Http\Request;

class WifiInfoController extends Controller
{
    public function index()
    {
        $wifiInfo = WifiInfo::all();
        return response()->json([
            'success' => 1,
            'message' => 'Get wifi info successfully',
            'data' => [
                'wifiInfo' => $wifiInfo,
            ],
        ], 200);
    }

    public function store(Request $request)
    {
        $wifiInfo = WifiInfo::create($request->all());

        return response()->json([
            'success' => 1,
            'message' => 'Create wifi info successfully',
            'data' => [
                'wifiInfo' => $wifiInfo,
            ],
        ], 200);
    }

    public function destroy(string $wifiInfoId)
    {
        $wifiInfo = WifiInfo::findOrFail($wifiInfoId);
        $wifiInfo->delete();

        return response()->json([
            'success' => 1,
            'message' => 'Delete wifi info successfully',
            'data' => [
                'wifiInfo' => $wifiInfo,
            ],
        ], 200);
    }

    public function edit(Request $request, string $wifiInfoId)
    {
        $wifiInfo = WifiInfo::findOrFail($wifiInfoId);
        $wifiInfo->update($request->all());

        return response()->json([
            'success' => 1,
            'message' => 'Edit wifi info successfully',
            'data' => [
                'wifiInfo' => $wifiInfo,
            ],
        ], 200);
    }

    public function getWifiInfoByClassroom(string $classroomId)
    {
        $wifiInfo = WifiInfo::where('classroomId', $classroomId)->first();
        return response()->json([
            'success' => 1,
            'message' => 'Get wifi info successfully',
            'data' => [
                'wifiInfo' => $wifiInfo,
            ],
        ], 200);
    }
}
