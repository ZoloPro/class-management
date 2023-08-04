<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentActivated
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $student = Auth::user();
        if ($student->isActived == 0) {
            return response()->json([
                'success' => 0,
                'message' => 'Your account is not actived',
                'data' => []
            ], 401);
        }
        return $next($request);
    }
}
