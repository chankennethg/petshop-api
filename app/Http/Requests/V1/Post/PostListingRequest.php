<?php

namespace App\Http\Requests\V1\Post;

use Illuminate\Foundation\Http\FormRequest;

class PostListingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|string>
     */
    public function rules(): array
    {
        return [
            'sortBy' => 'string',
            'desc' => 'in:true,false',
            'page' => 'integer',
            'limit' => 'integer',
        ];
    }
}
