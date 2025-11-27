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
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'catégorie',
            'city_id' => 'ville',
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
        ];
    }
}
