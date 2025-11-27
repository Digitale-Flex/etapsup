<?php

namespace App\Http\Requests;

use App\Models\RealEstate\Property;
use App\Rules\ValidateHashid;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\Period\Period;

class ReservationRequest extends FormRequest
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
            'property_id' => ['required', new ValidateHashid(Property::class)],
            'surname' => 'required|string|min:3|max:100',
            'name' => 'required|string|min:3|max:100',
            'phone' => 'required|min:8',
            'nationality' => 'required|min:4',
            'place_birth' => 'required|min:3',
            'date_birth' => 'required|date',
            'guests' => 'required|numeric',
            'status' => 'required',
            'period' => [
                'required',
                'array',
                'size:2',
                function ($attribute, $value, $fail) {
                    try {
                        $start = Carbon::parse($value[0])->startOfDay();
                        $end = Carbon::parse($value[1])->endOfDay();
                        $period = Period::make($start, $end);

                        // Vérifier si la propriété est disponible
                        $property = Property::findByHashidOrFail($this->property_id);
                        //    dd($property);
                        if (! $property->isAvailableForPeriod($start, $end)) {
                            $fail("La période choisie n'est pas disponible pour cette propriété. Veuillez sélectionner une autre date afin de finaliser votre demande");
                        }
                    } catch (\Exception $e) {
                        $fail('Erreur lors de la validation de la période.');
                    }
                },
            ],
            'period.0' => 'required|date_format:Y-m-d',
            'period.1' => 'required|date_format:Y-m-d',
            'reason' => 'required|string|min:3',
            'address' => 'nullable',
            'fees' => 'required|array',
            'amount' => 'required',
            'payment_method_id' => 'required',
        ];
    }

    public function attributes(): array
    {
        return [
            'surname' => 'nom',
            'name' => 'prénom(s)',
            'phone' => 'téléphone',
            'nationality' => 'nationalité',
            'place_birth' => 'lieu de naissance',
            'date_birth' => 'date de naissance',
            'guest ' => 'voyageur',
            'status' => 'statut',
        ];
    }

    public function messages(): array
    {
        return [
            'period.required' => 'La période de réservation est requise.',
            'period.array' => 'Format de période invalide.',
            'period.size' => 'La période doit contenir une date de début et de fin.',
            'period.*.date' => 'Les dates de la période sont invalides.',
        ];
    }
}
