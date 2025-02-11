<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
            'sex' => 'required|string|max:255',
            'age_group' => 'required|string|max:255',
            'client_type' => 'required|string|max:100',
            'digital_literacy' => 'boolean|max:10',
            'pwd' => 'boolean|max:10',
            'pregnant' => '|boolean|max:10',
            'senior_citizen' => 'boolean|max:10',
            // 'signature' => 'image|mimes:jpg,png,jpeg'
        ];
    }
}
