<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMissionRequest extends FormRequest
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

            'platform' => 'required|string',
            'comment' => 'required|string',
            'color_comment' => 'nullable|integer|between:0,3',
            'cumulative_per_year' => 'required|integer', //per year
            'plan_week_per_year' => 'required|integer', //per year
            'cumulative_per_month' => 'required|integer', ///per month
            'plan_week_per_month' => 'required|integer', //per month

            // Validates that the year is between 1900 and 2099 (my gust this is the range)
            'year' => [
                'required',
                'integer',
                'regex:/^(19|20)\d{2}$/'
            ],
            'month' => [
                'required',
                'integer',
                'between:1,12'
            ]
        ];
    }
}
