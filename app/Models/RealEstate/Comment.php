<?php

namespace App\Models\RealEstate;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\ModelStates\HasStates;

class Comment extends Model
{
    use HasStates;

    protected $fillable = [
        'user_id',
        'parent_id',
        'content',
        'score',
        'commentable_id',
        'commentable_type',
    ];

    protected $with = ['user'];

    protected $casts = [
        // 'status' => CommentState::class, // Utilise la classe d'état
        'parent_id' => 'integer',
    ];

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->with('user') // Eager loading des utilisateurs des réponses
            ->orderBy('created_at', 'asc');
    }

    public function isReply(): bool
    {
        return ! is_null($this->parent_id);
    }

    public function scopeForAuthor($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
