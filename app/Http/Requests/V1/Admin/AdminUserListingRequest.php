<?php

namespace App\Http\Requests\V1\Admin;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class AdminUserListingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // $user = Auth::user();
        // return $user->is_admin;

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
            'first_name' => 'string',
            'email' => 'email',
            'phone' => 'string',
            'address' => 'string',
            'created_at' => 'date|date_format:Y-m-d',
            'is_marketing' => 'in:0,1'
        ];
    }
}