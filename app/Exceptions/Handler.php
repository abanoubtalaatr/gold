<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Inertia\Inertia;

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




    if ($exception instanceof HttpException && $exception->getStatusCode() === 403) {
        return Inertia::render('Errors/403');
    }
    if ($exception instanceof HttpException && $exception->getStatusCode() === 404) {
        return Inertia::render('Errors/404');
    }
    if ($exception instanceof HttpException && $exception->getStatusCode() === 500) {
        return Inertia::render('Errors/500');
        # code...
    }
    return parent::render($request, $exception);
}

  protected function handleApiException(Throwable $exception)
    {

            \Log::error('API Exception:', ['exception' => $exception]);

        if ($exception instanceof ValidationException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $exception->errors(),
            ], 422);
        }

        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated',
            ], 401);
        }

        if ($exception instanceof NotFoundHttpException) {
            dd($exception);
            return response()->json([
                'status' => 'error',
                'message' => 'Resource not found',
            ], 404);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Something went wrong',
        ], 500);
    }

}