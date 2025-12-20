<?php

namespace App\Models;

use App\Models\Certificate\Coupon;
use App\Models\Certificate\Partner;
use App\Models\Certificate\RentalDeposit;
use App\Models\RealEstate\Category;
use App\Models\RealEstate\Layout;
use App\Models\RealEstate\PropertyType;
use App\Models\Settings\DegreeLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mtvs\EloquentHashids\HasHashid;
use Mtvs\EloquentHashids\HashidRouting;

class CustomSearch extends Model
{
    use HasHashid, HashidRouting, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'city_id',
        'partner_id',
        'coupon_id',
        'budget',
        'rental_start',
        'duration',
        'note',
        'paid',
        'stripe_payment_intent',
        'state',
        // 10 nouveaux champs questionnaire
        'gender',
        'passport_expiry_date',
        'address',
        'current_level_id',
        'preferred_language',
        'has_campus_france_experience',
        'has_diploma',
        'has_transcript',
        'has_cv',
        'has_motivation_letter',
        'has_conduct_certificate',
    ];

    protected function casts(): array
    {
        return [
            'rental_start' => 'date',
            'passport_expiry_date' => 'date',
            'has_campus_france_experience' => 'boolean',
            'has_diploma' => 'boolean',
            'has_transcript' => 'boolean',
            'has_cv' => 'boolean',
            'has_motivation_letter' => 'boolean',
            'has_conduct_certificate' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);

    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Pays visés (multi-sélection)
     */
    public function destinationCountries(): BelongsToMany
    {
        return $this->belongsToMany(
            Country::class,
            'custom_search_destination_country',
            'custom_search_id',
            'country_id'
        );
    }

    /**
     * Relation niveau d'études actuel
     */
    public function currentLevel(): BelongsTo
    {
        return $this->belongsTo(DegreeLevel::class, 'current_level_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function rentalDeposits(): BelongsToMany
    {
        return $this->belongsToMany(
            RentalDeposit::class,
            'custom_search_rental_deposit',
            'custom_search_id',
            'rental_deposit_id'
        );
    }

    public function layouts(): BelongsToMany
    {
        return $this->belongsToMany(
            Layout::class,
            'custom_search_layout',
            'custom_search_id',
            'layout_id'
        );
    }

    public function propertyTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            PropertyType::class,
            'custom_search_property_type',
            'custom_search_id',
            'property_type_id'
        );
    }
}
