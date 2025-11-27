<?php

namespace App\Http\Requests;

use App\Models\Country;
use App\Rules\ValidateHashid;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'country_id' => ['required', new ValidateHashid(Country::class)],
            'surname' => 'required|string|min:3|max:100',
            'name' => 'required|string|min:3|max:100',
            'phone' => 'required|min:8',
            'nationality' => 'required|min:4',
            'passport_number' => 'required|min:5',
            'place_birth' => 'required|min:3',
            'date_birth' => 'required|date',
        ];
    }
}
