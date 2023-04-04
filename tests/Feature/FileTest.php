<?php

namespace Tests\Feature;

use App\Exceptions\V1\ApiException;
use App\Models\File;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;


class FileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_user_can_upload_file(): void
    {
        /** @var User $user */
        $user = UserFactory::new()->create();

        $tokenHeader = $this->createTokenHeader($user->email, $user->id, $user->uuid);

        $file = UploadedFile::fake()->image('test.png');

        $this->post('/api/v1/file/upload', [
            'file' => $file,
        ], $tokenHeader)
        ->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                'name',
                'path',
                'size',
                'type',
                'uuid',
                'updated_at',
                'created_at'
            ],
            'error',
            'errors',
            'extra',
        ])
        ->assertJsonFragment([
            'success' => 1
        ]);
    }

    /**
     * A basic feature test example.
     */
    public function test_user_upload_file_with_invalid_token(): void
    {
        $tokenHeader = $this->createTokenHeader('email', 9, '1-1-1');

        $file = UploadedFile::fake()->image('test.png');

        $this->expectException(ApiException::class);

        $this->post('/api/v1/file/upload', [
            'file' => $file,
        ], $tokenHeader)
        ->assertStatus(401)
        ->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors',
            'trace'
        ])
        ->assertJsonFragment([
            'success' => 0,
            'error' => 'Unauthorized',
        ]);
    }

    /**
     * Test to check if user can download file
     *
     * @return void
     */
    public function test_user_can_download_file(): void
    {
        $file = UploadedFile::fake()->image('test.png');
        $output = $file->store(config('app.file_uploads_dir'));
        $fileRecord = new File();
        $fileRecord->name = $file->hashName();
        $fileRecord->path = (string) $output;
        $fileRecord->size = $file->getSize();
        $fileRecord->type = $file->getClientMimeType();
        $fileRecord->save();

        $this->get('/api/v1/file/' . $fileRecord->uuid)
        ->assertStatus(200)
        ->assertDownload($fileRecord->name);
    }

    /**
     * Test to check if user can download non-existing file
     *
     * @return void
     */
    public function test_user_cannot_download_missing_file(): void
    {
        $this->get('/api/v1/file/' . 'non-existing-uuid')
        ->assertStatus(404)
        ->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors',
            'trace'
        ])
        ->assertJsonFragment([
            'success' => 0,
            'error' => 'File not found'
        ]);
    }
}
