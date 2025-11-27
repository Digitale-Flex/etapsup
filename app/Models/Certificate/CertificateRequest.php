<?php

namespace App\Models\Certificate;

use App\Models\Charge;
use App\Models\City;
use App\Models\Country;
use App\Models\Scopes\PublishedScope;
use App\Models\User;
use App\Observers\CertificateRequestObserver;
use App\States\CertificateRequest\CertificateRequestState;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mtvs\EloquentHashids\HasHashid;
use Mtvs\EloquentHashids\HashidRouting;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\ModelStates\HasStates;
use Vinkla\Hashids\Facades\Hashids;

/**
 * @property mixed $id
 * @property mixed $user
 *
 * @method static whereState(string $string, string $class)
 */
#[ObservedBy([CertificateRequestObserver::class])]
class CertificateRequest extends Model implements HasMedia
{
    use ClearsResponseCache, HasHashid, HashidRouting, HasStates, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'user_id',
        'city_id',
        'rental_deposit_id',
        'country_id',
        'genre_id',
        'location_id', // deletable
        'coupon_id',
        'nationality',
        'passport_number',
        'budget',
        'rental_start',
        'duration',
        'further_information',
        'pay_later',
        'partner_id',
        'address',
        'paid',
        'stripe_payment_intent'
    ];

    protected function casts(): array
    {
        return [
            'rental_start' => 'date',
            'pay_later' => 'boolean',
            'solo' => 'boolean',
            'state' => CertificateRequestState::class,
        ];
    }

    protected function genreId(): Attribute
    {
        return Attribute::make(
            set: function (string $value) {
                if (app()->has('seeding') && app()->get('seeding')) {
                    return $value;
                }

                return collect(Hashids::decode($value))->first();
            }
        );
    }

    protected function cityId(): Attribute
    {
        return Attribute::make(
            set: function (string $value) {
                if (app()->has('seeding') && app()->get('seeding')) {
                    return $value;
                }

                return collect(Hashids::decode($value))->first();
            }
        );
    }

    protected function rentalDepositId(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                if (app()->has('seeding') && app()->get('seeding')) {
                    return $value;
                }

                // Si c'est un tableau, décoder tous les éléments
                if (is_array($value)) {
                    return collect($value)
                        ->map(fn ($id) => collect(Hashids::decode($id))->first())
                        ->filter()
                        ->toArray();
                }

                return collect(Hashids::decode($value))->first();
            }
        );
    }

    protected function countryId(): Attribute
    {
        return Attribute::make(
            set: function (string $value) {
                if (app()->has('seeding') && app()->get('seeding')) {
                    return $value;
                }

                return collect(Hashids::decode($value))->first();
            }
        );
    }

    protected function partnerId(): Attribute
    {
        return Attribute::make(
            set: function (string $value) {
                if (app()->has('seeding') && app()->get('seeding')) {
                    return $value;
                }

                return collect(Hashids::decode($value))->first();
            }
        );
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('certificate')
            ->singleFile();
        $this->addMediaCollection('contract')
            ->singleFile();
    }

    protected function reference(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->id,
        );
    }

    protected function file(): Attribute
    {
        return new Attribute(
            get: fn () => $this->getFirstMediaUrl('certificate')
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function rentalDeposit(): BelongsTo
    {
        return $this->belongsTo(RentalDeposit::class);
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    // deletable
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function charge(): HasOne
    {
        return $this->hasOne(Charge::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class)
            ->withDefault(['label' => 'Partenaire inconnu'])
            ->withoutGlobalScope(PublishedScope::class);
    }

    public function paymentProofs(): HasMany
    {
        return $this->hasMany(PaymentProof::class);
    }

    public function scopeOwner($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function rentalDeposits(): BelongsToMany
    {
        return $this->belongsToMany(
            RentalDeposit::class,
            'certificate_request_rental_deposit',
            'certificate_request_id',
            'rental_deposit_id'
        );
    }
}
