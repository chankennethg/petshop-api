<?php

namespace App\Http\Requests\V1\Admin;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class AdminCreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        return $user->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|string>
     */
    public function rules(): array
    {
        return [
           'first_name' => 'required|string',
           'last_name' => 'required|string',
           'email' => 'required|email|unique:users,email',
           'password' => 'required|string',
           'password_confirmation' => 'required|string|same:password',
           'address' => 'required|string',
           'phone_number' => 'required|string',
           'avatar' => 'nullable|exists:files,uuid',
           'is_marketing' => 'nullable|boolean',
        ];
    }
}
