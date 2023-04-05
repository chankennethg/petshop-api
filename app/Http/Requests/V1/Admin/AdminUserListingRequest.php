<?php

namespace App\Http\Requests\V1\Admin;

use App\Models\User;
use Auth;
use Illuminate\Foundation\Http\FormRequest;

class AdminUserListingRequest extends FormRequest
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
            'first_name' => 'string',
            'email' => 'email',
            'phone' => 'string',
            'address' => 'string',
            'created_at' => 'date|date_format:Y-m-d',
            'is_marketing' => 'in:0,1',
            'sortBy' => 'string',
            'desc' => 'in:true,false',
            'page' => 'integer',
            'limit' => 'integer',
        ];
    }
}
