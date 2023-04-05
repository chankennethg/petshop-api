<?php

namespace App\Http\Requests\V1\Admin;

use Auth;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class AdminEditUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var User $user*/
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
            'email' => 'required|email:rfc',
            'password' => 'required|string|confirmed|min:8',
            'avatar' => 'nullable|string|exists:files,uuid',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'is_marketing' => 'in:0,1',
        ];
    }
}
