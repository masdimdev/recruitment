<?php

namespace App\Exceptions;

use App\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param \Illuminate\Http\Request                   $request
     * @param \Illuminate\Validation\ValidationException $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return $this->errorResponse($exception->getMessage(), $exception->errors(), $exception->status);
    }

    /**
     * Prepare a JSON response for the given exception.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable               $e
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function prepareJsonResponse($request, \Throwable $e)
    {
        $errors = $this->convertExceptionToArray($e);

        return $this->errorResponse(
            $errors['message'],
            $errors,
            $this->isHttpException($e) ? $e->getStatusCode() : 500
        );
    }
}
