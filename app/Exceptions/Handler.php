<?php

namespace App\Exceptions;

use Throwable;
use ErrorException;
use Illuminate\Http\Request;
use App\Exceptions\Actions\TransformToJson;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
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

        /** Will serve as fallback to uncatched errors in ApiHandler */
        $this->renderable(function (Throwable $e, Request $request) {
            if ($e instanceof UnauthorizedHttpException) {
                return TransformToJson::handle(401, 0, [], $e->getMessage());
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return TransformToJson::handle(405, 0, [], $e->getMessage(), [], $e->getTrace());
            }

            if ($e instanceof AccessDeniedHttpException) {
                return TransformToJson::handle(403, 0, [], $e->getMessage(), [], $e->getTrace());
            }

            if ($e instanceof ValidationException) {
                return TransformToJson::handle(422, 0, [], 'Validation Error', $e->errors(), $e->getTrace());
            }

            if ($e instanceof NotFoundHttpException) {
                return TransformToJson::handle(404, 0, [], 'Not Found');
            }

            if ($e instanceof ErrorException) {
                return TransformToJson::handle(500, 0, [], $e->getMessage(), [], $e->getTrace());
            }

            if ($e instanceof ModelNotFoundException) {
                return TransformToJson::handle(404, 0, [], 'Not Found', [], $e->getTrace());
            }
        });
    }
}
