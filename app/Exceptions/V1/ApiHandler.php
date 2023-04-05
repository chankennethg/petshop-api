<?php

namespace App\Exceptions\V1;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Exceptions\Actions\TransformToJson;

/**
 * Custom API Exception Handler Class
 */
class ApiHandler extends Exception
{
    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var array<string,mixed>
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
     * @param array<string,mixed> $data
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(): JsonResponse
    {
        $trace = config('app.debug') ? $this->getTrace() : [];

        return TransformToJson::handle(
            $this->statusCode,
            0,
            $this->data,
            $this->getMessage(),
            $this->errors,
            $trace
        );
    }
}
