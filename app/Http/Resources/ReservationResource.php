<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
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
            'price' => $this->price,
            'guests' => $this->whenHas('guests'),
            'reason' => $this->whenHas('reason'),
            'address' => $this->whenHas('address'),
            'fees' => $this->whenHas('fees'),
            'start_date' => $this->whenHas('start_date'),
            'end_date' => $this->whenHas('end_date'),
            'type' => $this->type,
            'created_at' => $this->created_at,
            'status' => $this->getReservationStatus(),
            'property' => new PropertyResource($this->whenLoaded('property')),
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
            ],
                'user_rating' => $this->when(auth()->check() && $this->relationLoaded('ratings'),
                    fn () => $this->ratings->where('user_id', auth()->id())->first()?->score
                ),
        ];
    }

    protected function getReservationStatus(): array
    {
        $today = Carbon::today();
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        if ($today->lt($startDate)) {
            return ['value' => 'upcoming', 'label' => 'à venir']; // Réservation à venir
        } elseif ($today->between($startDate, $endDate)) {
            return ['value' => 'progress', 'label' => 'en cours']; // Réservation en cours
        } else {
            return ['value' => 'completed', 'label' => 'terminée']; // Réservation terminée
        }
    }
}
