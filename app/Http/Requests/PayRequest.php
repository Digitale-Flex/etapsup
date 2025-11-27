<?php

namespace App\Http\Requests;

use App\Models\Certificate\Coupon;
use App\Models\Certificate\Genre;
use App\Models\Certificate\Partner;
use App\Models\Certificate\RentalDeposit;
use App\Models\City;
use App\Models\Country;
use App\Rules\ValidateHashid;
use Illuminate\Foundation\Http\FormRequest;

class PayRequest extends FormRequest
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
            'country_birth_id' => ['required', new ValidateHashid(Country::class)],
            'genre_id' => ['required', new ValidateHashid(Genre::class)],
            'city_id' => ['required', new ValidateHashid(City::class)],
            'partner_id' => ['required_if:solo,false', new ValidateHashid(Partner::class)],
            'rental_deposit_ids' => ['required', 'array', 'min:1'],
            'rental_deposit_ids.*' => ['required', new ValidateHashid(RentalDeposit::class)],
            'coupon_id' => ['nullable',  new ValidateHashid(Coupon::class)],
          //  'payment_method_id' => 'required|string',
            'surname' => 'required|string|min:3|max:100',
            'name' => 'required|string|min:3|max:100',
            'solo' => 'boolean',
            'phone' => 'required|min:8',
            'nationality' => 'required|min:4',
            'budget' => 'required|min:3',
            'passport_number' => 'required|min:5',
            'place_birth' => 'required|min:3',
            'date_birth' => 'required|date',
            'rental_start' => 'required|date',
            'duration' => 'required',
            'furtherInformation' => '',
        ];
    }
}
