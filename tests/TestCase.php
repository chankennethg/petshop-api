<?php

namespace Tests;

use App\Models\File;
use App\Http\Services\Jwt\JwtAuth;
use Database\Factories\FileFactory;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        if (File::count() === 0) FileFactory::new()->count(1)->create();
    }


    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    protected function getSuccessResponse($data): array
    {
        return [
            'success' => 1,
            'data' => $data,
            'error' => null,
            'errors' => [],
            'extra' => []
        ];
    }

    /**
     * @param string|null $error
     * @param array<mixed,mixed> $errors
     * @param array<mixed,mixed> $trace
     * @return array<string,mixed>
     */
    protected function getFailResponse($error = null, $errors = [], $trace = [])
    {
        return [
            'success' => 0,
            'data' => [],
            'error' => $error,
            'errors' => $errors,
            'extra' => $trace
        ];
    }

    /**
     * Create token per user
     *
     * @param string $email
     * @param int $id
     * @param string $uuid
     * @return array<string,string>
     */
    protected function createTokenHeader($email, $id, $uuid): array
    {
        $token = ($this->app->make(JwtAuth::class))->createToken([
            'uuid' => $uuid,
            'email' => $email
        ],(string) $id);

        return [
            'Authorization' => "Bearer {$token}"
        ];
    }
}
