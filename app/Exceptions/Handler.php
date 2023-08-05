<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {

        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'success' => 0,
                'message' => 'Entry for ' . str_replace('App\\', '', $exception->getModel()) . ' not found'], 404);

        } else if ($exception->getCode() == 23000) {
            return response()->json([
                'susscess' => 0,
                'message' => 'Foreign Key Constraint Violation',
                'errorCode' => 2300], 400);
        } else if ($exception instanceof ValidationException) {
            $errorCollect = collect($exception->errors());
            return response()->json([
                'success' => 0,
                'message' => $errorCollect->first()], 200);
        }
        return parent::render($request, $exception);
    }
}
