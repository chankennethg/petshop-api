<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use App\Traits\ApiTransformer;
use ErrorException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    use ApiTransformer;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     *
     * @var array<mixed,mixed>
     */
    protected $data;

    /**
     * @var array<mixed,mixed>
     */
    protected $errors;


    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
        $this->reportable(function (Throwable $e): void {
            //
        });

        $this->renderable(function (Throwable $e, Request $request) {
            if ($e instanceof UnauthorizedHttpException) {
                return $this->toResponse(401, 0, [], $e->getMessage());
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return $this->toResponse(405, 0, [], $e->getMessage(), [], $e->getTrace());
            }

            if ($e instanceof AccessDeniedHttpException) {
                return $this->toResponse(403, 0, [], $e->getMessage(), [], $e->getTrace());
            }

            if ($e instanceof ValidationException) {
                return $this->toResponse(422, 0, [], 'Validation Error', $e->errors(), $e->getTrace());
            }

            if ($e instanceof NotFoundHttpException) {
                return $this->toResponse(404, 0, [], 'Not Found');
            }

            if ($e instanceof ErrorException) {
                return $this->toResponse(500, 0, [], $e->getMessage(), [], $e->getTrace());
            }
        });
    }
}
