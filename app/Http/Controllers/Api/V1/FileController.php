<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\V1\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\FileRequest;
use App\Models\File;
use App\Traits\ApiTransformer;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    use ApiTransformer;

    /**
     * Upload a file
     *
     * @param FileRequest $request
     * @return JsonResponse
     */
    public function upload(FileRequest $request): JsonResponse
    {
        /** @var UploadedFile $file */
        $file = $request->file('file');

        try {
            $output = $file->store(config('app.file_uploads_dir'));

            $fileRecord = new File();
            $fileRecord->name = $file->hashName();
            $fileRecord->path = (string) $output;
            $fileRecord->size = $this->bytesToHumanReadable($file->getSize());
            $fileRecord->type = $file->getClientMimeType();
            $fileRecord->save();

        } catch(Exception $e) {
            throw new ApiException(500, 'Internal Server Error');
        }

        return $this->toResponse(200, 1, $fileRecord->toArray());
    }

    /**
     * Download a file via uuid
     *
     * @param string $uuid
     * @return StreamedResponse|JsonResponse
     */
    public function download(string $uuid): StreamedResponse|JsonResponse
    {
        try {
            $file = File::where('uuid', $uuid)->firstOrFail();
        } catch(ModelNotFoundException $e) {
            return $this->toResponse(404, 0, [], 'File not found');
        }

        return Storage::download($file->path, $file->name);
    }

    /**
     * Convert bytes to human readable
     *
     * @param int $bytes
     * @return string
     */
    private function bytesToHumanReadable(int $bytes): string
    {
        $units = ['bytes', 'KB', 'MB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
