<?php

namespace App\Models\RealEstate;

use App\Enums\ReservationType;
use App\Jobs\RealEstate\GenerateContractJob;
use App\Mail\ContractGenerated;
use App\Models\Rating;
use App\Models\StatusChange;
use App\Models\User;
use App\Observers\ReservationObserver;
use App\Services\ContractService;
use App\States\Reservation\Confirmed;
use App\States\Reservation\Pending;
use App\States\Reservation\ReservationState;
use App\Traits\ClearsResponseCache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Mail;
use Mtvs\EloquentHashids\HasHashid;
use Mtvs\EloquentHashids\HashidRouting;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\ModelFlags\Models\Concerns\HasFlags;
use Spatie\ModelFlags\Models\Flag;
use Spatie\ModelStates\HasStates;
use Spatie\Period\Period;

#[ObservedBy([ReservationObserver::class])]
class Reservation extends Model implements HasMedia
{
    use ClearsResponseCache, HasFlags, HasHashid, HashidRouting, InteractsWithMedia, SoftDeletes, HasStates;

    protected $fillable = [
        'property_id',
        'user_id',
        'start_date',
        'end_date',
        'guests',
        'status',
        'price',
        'status',
        'stripe_payment_intent_id',
        'reason',
        'notes',
        'address',
        'fees',
        'state',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'fees' => 'json',
            'start_date' => 'date',
            'end_date' => 'date',
            'guests' => 'integer',
            'price' => 'decimal:2',
            'state' => ReservationState::class,
            'type' => ReservationType::class,
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper pour créer un Period object
    public function getPeriodAttribute(): Period
    {
        return Period::make($this->start_date, $this->end_date);
    }

    // Scopes pour la recherche de réservations
    public function scopeOverlappingWithPeriod($query, Period $period)
    {
        return $query->where(function ($query) use ($period) {
            $query->where(function ($q) use ($period) {
                $q->where('start_date', '<=', $period->start()->format('Y-m-d'))
                    ->where('end_date', '>=', $period->end()->format('Y-m-d'));
            });
        });
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'ratingable');
    }

    public function scopeAuthor($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    // Scope pour les réservations à venir
    public function scopeUpcoming($query)
    {
        $today = Carbon::today()->toDateString();

        return $query->where('start_date', '>', $today);
    }

    // Scope pour les réservations en cours
    public function scopeInProgress($query)
    {
        $today = Carbon::today()->toDateString();

        return $query->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today);
    }

    // Scope pour les réservations terminées
    public function scopeCompleted($query)
    {
        $today = Carbon::today()->toDateString();

        return $query->where('end_date', '<', $today);
    }

    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('score') ?? 0;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('contract')
            ->singleFile();

        $this->addMediaCollection('files')
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/webp',
                'application/pdf',
            ]);
    }


    public function flags(): MorphMany
    {
        return $this->morphMany(Flag::class, 'flaggable');
    }
    public function scopeWithPdfGenerated($query)
    {
        return $query->flagged('pdf_generated');
    }

    public function scopeWithEmailSent($query)
    {
        return $query->flagged('email_sent');
    }

    public function scopeWithPdfGenerationIssues($query)
    {
        return $query->flagged('pdf_generation_failed');
    }

    public function scopeWithEmailIssues($query)
    {
        return $query->flagged('email_failed');
    }

    public function scopeWithoutFlags(Builder $query, array $flags): Builder
    {
        return $query->where(function ($query) use ($flags) {
            foreach ($flags as $flag) {
                $query->whereNull("flags->$flag");
            }
        });
    }

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function generateContract(): void
    {
        try {
            $service = new ContractService;
            $service->generateContract($this);
            $this->unflag('pdf_generation_failed');
            $this->flag('pdf_generated');
        } catch (\Exception $e) {
            $this->unflag('pdf_generated');
            $this->flag('pdf_generation_failed');
            throw $e;
        }
    }

    public function sendContractEmail(): void
    {
        try {
            if (! $this->hasFlag('pdf_generated')) {
                throw new \Exception('Le contrat doit être généré avant l\'envoi');
            }

            Mail::to($this->user->email)->send(new ContractGenerated($this));
            $this->unflag('email_failed');
            $this->flag('email_sent');
        } catch (\Exception $e) {
            $this->unflag('email_sent');
            $this->flag('email_failed');
            throw $e;
        }
    }

    public function dispatchContractGeneration(): void
    {
        GenerateContractJob::dispatch($this->id)
            ->onQueue('contracts')
            ->delay(now()->addSeconds(10));
    }

    public function scopePending($query): void
    {
        $query->whereState('state', Pending::class);
    }

    public function scopeActive($query): void
    {
        $query->whereState('state', [Pending::class, Confirmed::class]);
    }

    public function statusChanges(): MorphMany
    {
        return $this->morphMany(StatusChange::class, 'status_changeable');
    }

    public function statusHistory(): \Illuminate\Support\Collection
    {
        return $this->statusChanges()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($change) {
                return [
                    'title' => "Changement : {$change->from_state} → {$change->to_state}",
                    'date' => $change->created_at->format('d/m/Y H:i'),
                    'notes' => $change->notes,
                ];
            });
    }

    public function getIsCompletedAttribute(): bool
    {
        return Carbon::now()->gt($this->end_date);
    }
}
