<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class   EnsureInClassroom
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $classroomId = $request->classroomId;
        $student = Auth::user();
        $classroom = $student->registeredClassrooms->find($classroomId);
        if (!$classroom) {
            return response()->json([
                'success' => 0,
                'message' => 'You are not in this classroom',
                'data' => [],
            ], 403);
        }
        return $next($request);
    }
}
