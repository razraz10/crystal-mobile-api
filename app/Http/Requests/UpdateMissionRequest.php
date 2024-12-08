<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMissionRequest extends FormRequest
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
            'platform' => 'nullable|string',
            'comment' => 'nullable|string',
            'cumulative_per_year' => 'nullable|integer', //per year
            'plan_week_per_year' => 'nullable|integer', //per year
            'cumulative_per_month' => 'nullable|integer', ///per month
            'plan_week_per_month' => 'nullable|integer', //per month
            'color_comment' => 'nullable|integer|between:0,3',

            // Validates that the year is between 1900 and 2099 (my gust this is the range)
            'year' => [
                'nullable',
                'integer',
                'regex:/^(19|20)\d{2}$/'
            ],
            'month' => [
                'nullable',
                'integer',
                'between:1,12'
            ]
        ];
    }
}
