<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\File;
use App\Exceptions\V1\ApiHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\V1\FileUploadRequest;
use App\Http\Resources\V1\File\FileUploadResource;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FileController extends Controller
{
    /**
     * Upload a file
     *
     * @param FileUploadRequest $request
     * @return FileUploadResource
     */
    public function upload(FileUploadRequest $request): FileUploadResource
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
            throw new ApiHandler(500, 'Internal Server Error');
        }

        return new FileUploadResource($fileRecord);
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
            throw new ApiHandler(404, 'File not found');
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
