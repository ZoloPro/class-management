<?php

namespace App\Http\Middleware;

use App\Models\Classroom;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClassroomInAvailableTime
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $classroomId = $request->route('classroomId');
        $classroom = Classroom::find($classroomId);
        $curDate = date('Y-m-d');
        if ($curDate < $classroom->startDate || $curDate > $classroom->endDate) {
            return response()->json([
                'success' => 0,
                'message' => 'Class is out of time',
                'data' => [],
            ], 400);
        }
        return $next($request);
    }
}
