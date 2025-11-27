<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Refonte: Story 1.1.1 - Event Registration Form Request
class EventRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow public event registration
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'email' => ['required', 'email', 'max:255'],
            'country' => ['required', 'string', 'max:10'],
            'phone' => ['nullable', 'string', 'max:20'],
            'study_level' => ['nullable', 'string', 'max:20'],
            'consent' => ['required', 'accepted']
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire',
            'name.min' => 'Le nom doit contenir au moins 2 caractères',
            'email.required' => 'L\'email est obligatoire',
            'email.email' => 'Veuillez saisir une adresse email valide',
            'country.required' => 'Le pays est obligatoire',
            'consent.accepted' => 'Vous devez accepter les conditions pour continuer'
        ];
    }
}
