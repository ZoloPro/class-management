<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class WifiInfoController extends Controller
{
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
