<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'personal_number' => [
                'required',
                'regex:/^\d{7}$/',
            ],
            'phone_number' => [
                'required',
                'regex:/^05[0-9]{8}$/',
            ],
            'employee_type' => [
                'required',
                'integer',
                'between:1,4'
            ],
            ///permission_code must be valid.
            'permission_code' => 'required|exists:permissions,code_permission,is_deleted,0',
        ];
    }
}
