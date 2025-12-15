<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Certificate\CertificateRequest;
use App\Models\Certificate\Partner;
use App\Traits\ClearsResponseCache;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Laravolt\Avatar\Avatar;
use Mtvs\EloquentHashids\HasHashid;
use Mtvs\EloquentHashids\HashidRouting;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\ModelFlags\Models\Concerns\HasFlags;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class User extends Authenticatable implements FilamentUser, HasMedia
{
    use Billable, ClearsResponseCache, HasApiTokens, HasHashid, HashidRouting, HasRoles, HasSlug, InteractsWithMedia, Notifiable, SoftDeletes, HasFlags;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    // Sprint1 Feature 1.6.1 — Gestion des comptes (admin, partenaire, gestionnaire)
    protected $fillable = [
        'partner_id',
        'country_id',
        'address_id',
        'gender',
        'surname',
        'name',
        'email',
        'phone',
        'place_birth',
        'date_birth',
        'created_ip_address',
        'last_ip_address',
        'last_login',
        'password',
        'passport_number',
        'nationality',
        'is_active',
        'is_partner_manager',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_birth' => 'date',
            'password' => 'hashed',
            'is_partner_manager' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(['name', 'surname'])
            ->saveSlugsTo('slug');
    }

    // Sprint1 Feature 1.6.1 — Accès panel par rôle (admin, dev, gestionnaire, manager)
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasAnyRole(['admin', 'dev', 'gestionnaire', 'manager']); // Fix A7: 'account' → 'gestionnaire'
        }

        // Accès au panel partner uniquement pour le rôle partner
        if ($panel->getId() === 'partner') {
            return $this->hasRole('partner');
        }

        return false;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')
            ->useFallbackUrl((new Avatar)->create($this->full_name)->toBase64())
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('thumb')
                    ->fit(Fit::Crop, 300, 300);
            });
    }

    protected function surname(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucwords(strtolower($value)),
            set: fn (string $value) => ucwords(strtolower($value))
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucwords(strtolower($value)),
            set: fn (string $value) => ucwords(strtolower($value))
        );
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => strtolower($value),
            set: fn (string $value) => strtolower($value)
        );
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function certificateRequests(): HasMany
    {
        return $this->hasMany(CertificateRequest::class);
    }

    /**
     * Les candidatures de l'étudiant (EtatSup Phase 4)
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'user_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function scopeForPartner($query, ?User $user = null)
    {
        $user = $user ?? auth()->user();
        return $user->hasRole('partner')
            ? $query->where('partner_id', $user->partner_id)
            : $query;
    }

    public function isManager(): bool
    {
        return $this->is_partner_manager;
    }
}
