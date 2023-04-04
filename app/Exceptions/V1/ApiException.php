<?php

namespace App\Exceptions\V1;

use App\Traits\ApiTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiException extends Exception
{
    use ApiTransformer;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     *
     * @var array<mixed,mixed>
     */
    protected $data;

    /**
     * @var array<mixed,mixed>
     */
    protected $errors = [];

    /**
     * Class constructor
     *
     * @param int $statusCode
     * @param string $message
     * @param array<mixed,mixed> $data
     */
    public function __construct(int $statusCode = 400, string $message = "", array $data = [])
    {
        $this->statusCode = $statusCode;
        $this->data = $data;
        parent::__construct($message);
    }

    /**
     * get Status code
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Exception renderer
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(Request $request): JsonResponse
    {
        $trace = config('app.debug') === false ? [] : $this->getTrace();

        return $this->toResponse(
            $this->statusCode,
            0,
            $this->data,
            $this->getMessage(),
            $this->errors,
            $trace
        );
    }
}
