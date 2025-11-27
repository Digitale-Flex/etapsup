<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'score' => $this->score,
            'author' => [
                'name' => $this->user->name,
                'avatar' => $this->user->getFirstMediaUrl('avatar', 'thumb'),
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'replies_count' => $this->when(! $this->isReply(), $this->replies()->count()),
            'replies' => CommentResource::collection($this->when(! $this->isReply(), $this->replies)),
            'parent_id' => $this->when($this->isReply(), $this->parent_id),
        ];
    }
}
