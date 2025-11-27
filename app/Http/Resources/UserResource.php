<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->hashid,
            'surname' => $this->whenHas('surname'),
            'name' => $this->whenHas('name'),
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->whenHas('phone'),
            'place_birth' => $this->whenHas('place_birth'),
            'date_birth' => $this->whenHas('date_birth'),
            'nationality' => $this->whenHas('nationality'),
            'passport_number' => $this->whenHas('passport_number'),
            'photo' => $this->getFirstMediaUrl('photo', 'thumb'),
            'country' => new CountryResource($this->whenLoaded('country')),
        ];
    }
}
