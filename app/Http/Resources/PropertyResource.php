<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->whenHas('description'),
            'price' => $this->whenHas('price'),
            'cleaning_fees' => $this->whenHas('cleaning_fees'),
            'room' => $this->whenHas('room'),
            'bathroom' => $this->whenHas('bathroom'),
            'dining_room' => $this->whenHas('dining_room'),
            'kitchen' => $this->whenHas('kitchen'),
            'living_room' => $this->whenHas('living_room'),
            'balcony' => $this->whenHas('balcony'),
            'outdoor_space' => $this->whenHas('outdoor_space'),
            'address' => $this->whenHas('address'),
            'regulation' => $this->whenHas('regulation'),
            'discount' => $this->whenHas('discount'),
            'airbnb' => $this->whenHas('airbnb'),
            'propertyType' => new PropertyTypeResource($this->whenLoaded('propertyType')),
            'subCategory' => new SubCategoryResource($this->whenLoaded('subCategory')),
            'category' => new SubCategoryResource($this->whenLoaded('category')),
            'city' => new CityResource($this->whenLoaded('city')),
            'equipments' => EquipmentResource::collection($this->whenLoaded('equipments')),
            'regulations' => RegulationResource::collection($this->whenLoaded('regulations')),
            'layouts' => LayoutResource::collection($this->whenLoaded('layouts')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'ratings' => [
                'average' => round($this->whenLoaded('ratings', fn () => $this->ratings->avg('score'), 0), 1),
                'count' => $this->whenLoaded('ratings', fn () => $this->ratings->count(), 0),
                'distribution' => $this->when($this->relationLoaded('ratings'), function () {
                    $totalRatings = $this->ratings->count();

                    return collect(range(5, 1))->mapWithKeys(function ($stars) use ($totalRatings) {
                        $ratingCount = $this->ratings->where('score', $stars)->count();

                        return [
                            $stars => [
                                'count' => $ratingCount,
                                'percentage' => $totalRatings > 0 ? ($ratingCount / $totalRatings) * 100 : 0,
                            ],
                        ];
                    });
                }),
                'user_rating' => $this->when(auth()->check() && $this->relationLoaded('ratings'),
                    fn () => $this->ratings->where('user_id', auth()->id())->first()?->score
                ),
            ],
            //'thumb' => $this->getMedia('images')->first()?->getUrl(),
            'thumb' => $this->getFirstMediaUrl('images', 'thumb'),
            'images' => $this->when(
                $request->route()->named('properties.show') || $request->route()->named('properties.preview') || $request->route()->named('dashboard.reservations.show'),
                $this->getMedia('images')
            ),
        ];
    }
}
