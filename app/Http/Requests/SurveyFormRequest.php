<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SurveyFormRequest extends FormRequest
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
            //
            'office_id' => 'required|integer|exists:offices,id',
            'division_id' => 'required|integer|exists:divisions,id',

            //customer validation
            'email' => 'nullable|email|max:255',
            'name' => 'nullable|string|max:255',
            'sex' => 'required|string|max:255',
            'age_group' => 'required|string|max:255',
            'client_type' => 'required|string|max:100',
            'pwd' => 'boolean|max:10',
            'pregnant' => 'boolean|max:10',
            'senior_citizen' => 'boolean|max:10',

            //customer attribute rating validation
            'rate_score' => 'nullable|integer|max:10',
            'importance_rate_score' => 'integer|max:10',

            //customer cc rating validation 
            'answer' => 'nullable|integer|max:255',

            //customer comment validation
            'comment' => 'nullable|string|max:255',
            'is_complaint' => 'boolean',

            //customer toher data validation
            'indication' => 'nullable|string|max:255',
            'recommend_rate_score' => 'required|max:255',

            'captcha' => 'required|captcha',

            'signature' => 'nullable|string',
            
        ];
    }
}
