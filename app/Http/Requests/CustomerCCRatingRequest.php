<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerCCRatingRequest extends FormRequest
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
            'id' => 'required|exists:customer_c_c_ratings,id',
            'customer_id' => 'required|exists:customers,id',
            'cc_id' => 'required|exists:cc_questions,id', 
            'answer' => 'nullable|integer|max:255',
        ];
    }

}
