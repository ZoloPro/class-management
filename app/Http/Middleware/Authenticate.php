<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function unauthenticated($request, array $guards)
    {
        abort(response()->json([
            'success' => 0,
            'message' => 'Unauthenticated',
            'data' => []], 401));
    }
}
