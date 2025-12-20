<?php

namespace App\Http\Requests;

use App\Models\Certificate\Coupon;
use App\Models\Certificate\Partner;
use App\Models\Certificate\RentalDeposit;
use App\Models\City;
use App\Models\Country;
use App\Models\RealEstate\Category;
use App\Models\RealEstate\Layout;
use App\Models\RealEstate\PropertyType;
use App\Models\Settings\DegreeLevel;
use App\Rules\ValidateHashid;
use Illuminate\Foundation\Http\FormRequest;

class CustomSearchRequest extends FormRequest
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
            'category_id' => ['required', new ValidateHashid(Category::class)],
            'city_id' => ['required', new ValidateHashid(City::class)],
            'destination_country_id' => ['nullable', new ValidateHashid(Country::class)], // Bug 6 Fix
            'partner_id' => ['required', new ValidateHashid(Partner::class)],
            'coupon_id' => ['nullable', new ValidateHashid(Coupon::class)],
            'paid' => ['nullable', 'numeric', 'not_in:0'],
            'country_birth_id' => ['nullable', new ValidateHashid(Country::class)],
            'rental_deposit_ids' => ['required', 'array', 'min:1'],
            'rental_deposit_ids.*' => ['required', new ValidateHashid(RentalDeposit::class)],

            'layout_ids' => ['required', 'array', 'min:1'],
            'layout_ids.*' => ['required', new ValidateHashid(Layout::class)],

            'property_type_ids' => ['required', 'array', 'min:1'],
            'property_type_ids.*' => ['required', new ValidateHashid(PropertyType::class)],

            'surname' => 'required|string|min:3|max:100',
            'name' => 'required|string|min:3|max:100',
            'phone' => 'required|min:8',
            'nationality' => 'required|min:4',
            'budget' => 'required|min:3',
            'passport_number' => 'required|min:5',
            'place_birth' => 'required|min:3',
            'date_birth' => 'required|date',
            'rental_start' => 'required|date',
            'duration' => 'required',
            'note' => 'nullable',

            // 10 nouveaux champs questionnaire (tous optionnels)
            'gender' => 'nullable|in:M,F',
            'passport_expiry_date' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'current_level_id' => ['nullable', new ValidateHashid(DegreeLevel::class)],
            'preferred_language' => 'nullable|in:FR,EN',
            'has_campus_france_experience' => 'nullable|boolean',
            'has_diploma' => 'nullable|boolean',
            'has_transcript' => 'nullable|boolean',
            'has_cv' => 'nullable|boolean',
            'has_motivation_letter' => 'nullable|boolean',
            'has_conduct_certificate' => 'nullable|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'catégorie',
            'city_id' => 'ville',
            'destination_country_id' => 'pays de destination', // Bug 6 Fix
            'partner_id' => 'partenaire',
            'coupon_id' => 'coupon',
            'paid' => 'montant payé',
            'country_birth_id' => 'pays de naissance',
            'rental_deposit_ids' => 'dépôts de garantie',
            'rental_deposit_ids.*' => 'dépôt de garantie',
            'layout_ids' => 'Commodités',
            'layout_ids.*' => 'Commodité',
            'property_type_ids' => 'types de propriétés',
            'property_type_ids.*' => 'type de propriété',
            'surname' => 'Nom',
            'name' => 'prénom',
            'phone' => 'téléphone',
            'nationality' => 'nationalité',
            'budget' => 'budget',
            'passport_number' => 'numéro de passeport',
            'place_birth' => 'lieu de naissance',
            'date_birth' => 'date de naissance',
            'rental_start' => 'début de location',
            'duration' => 'durée',
            'note' => 'notes',

            // 10 nouveaux champs
            'gender' => 'sexe',
            'passport_expiry_date' => 'date d\'expiration du passeport',
            'address' => 'adresse complète',
            'current_level_id' => 'niveau d\'études actuel',
            'preferred_language' => 'langue préférée',
            'has_campus_france_experience' => 'expérience Campus France',
            'has_diploma' => 'dernier diplôme',
            'has_transcript' => 'relevés de notes',
            'has_cv' => 'CV',
            'has_motivation_letter' => 'lettre de motivation',
            'has_conduct_certificate' => 'attestation de bonne vie et moeurs',
        ];
    }
}
