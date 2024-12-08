<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMarketRequest extends FormRequest
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
            'id_num' => 'nullable|unique:markets,id_num',
            'name_meshek' => 'nullable|unique:markets,name_meshek',

            // Validates that the year is between 1900 and 2099 (my gust this is the range)
            'year' => [
                'nullable',
                'integer',
                'regex:/^(19|20)\d{2}$/'
            ],

            'color_comment' => 'nullable|integer|between:0,3',
            'comment' => 'nullable|string',
            'is_open' => 'nullable|boolean',
            'expired_agreement' => [
                'nullable',
                'date',
            ],
        ];
    }
}
