<?php

namespace App\Models;

use App\Models\Certificate\Coupon;
use App\Models\Certificate\Partner;
use App\Models\Certificate\RentalDeposit;
use App\Models\RealEstate\Category;
use App\Models\RealEstate\Layout;
use App\Models\RealEstate\PropertyType;
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
    ];

    protected function casts(): array
    {
        return [
            'rental_start' => 'date',
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
