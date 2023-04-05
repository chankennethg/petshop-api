<?php

namespace App\Http\Requests\V1;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return !$user;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|string>
     */
    public function rules(): array
    {
        // Max 10mb file size
        return [
            'file' => 'required|image|max:10240',
        ];
    }
}
