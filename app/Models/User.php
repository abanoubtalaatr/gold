<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Traits\IsActive;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Wotz\VerificationCode\VerificationCode;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Services\VerificationCode as VerificationCodeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens,
        HasFactory,
        Notifiable,
        HasRoles,
        IsActive,
        // InteractsWithMedia,
        SoftDeletes;

    use CanResetPasswordTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'created_at',
        'is_active',
        'mobile',
        'dialling_code',
        'social_provider',
        'last_login_at',
        'accept_terms',
        'mobile_verified_at',
        'email_verified_at',
        'avatar',
        'updated_at',
        'vendor_id',
        'latitude',
        'longitude',
        'store_name_en',
        'store_name_ar',
        'commercial_registration_number',
        'commercial_registration_image',
        'iban',
        'working_hours',
        'vendor_status',
        'rejection_reason',
        'logo',
        'city_id',
        'prefer_language',

    ];

    protected $appends = ['avatar'];

    public function getAvatarAttribute()
    {
        if (isset($this->attributes['avatar']))
            return asset("storage/{$this->attributes['avatar']}");

        return null;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'created_at' => 'date:Y-m-d',
    ];

    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format('d M Y');
    }

    public function sendPasswordResetNotification($token): void
    {
        if (strlen($token) === 6 && is_numeric($token)) {
            $this->notify(new \App\Notifications\ResetPasswordOTP($token));
        } else {
            $this->notify(new ResetPasswordNotification($token));
        }
    }

    /**
     * Send the registration verification email.
     */
    public function sendEmailVerificationNotification(): void
    {
        //implements ShouldQueue
        VerificationCode::send($this->email);
    }

    /**
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * @return bool
     */
    public function isMobileVerified(): bool
    {
        return $this->mobile_verified_at !== null;
    }

    public function scopeVerified($query)
    {

        return $query->whereNotNull('mobile_verified_at');
    }

    public function isAdmin()
    {
        return $this->hasAnyRole(['superadmin']);
    }


    public function isVendor()
    {
        return $this->hasAnyRole(['vendor']);
    }
    public function deviceTokens()
    {
        return $this->morphMany('App\Models\DeviceToken', 'tokenable');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function defaultAddress()
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function goldPieces()
    {
        return $this->hasMany(GoldPiece::class, 'user_id');
    }

    /**
     * Get the user's favorite gold pieces.
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get the gold pieces that the user has favorited.
     */
    public function favoritedGoldPieces()
    {
        return $this->belongsToMany(GoldPiece::class, 'favorites')
            ->withTimestamps();
    }

    public function orderRentals()
    {
        return $this->hasMany(OrderRental::class);
    }

    public function orderSales()
    {
        return $this->hasMany(OrderSale::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class, 'vendor_id');
    }


    public function getStoreNameAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->store_name_ar : $this->store_name_en;
    }

    public function getCommercialRegistrationImageUrlAttribute()
    {
        return $this->commercial_registration_image
            ? Storage::url($this->commercial_registration_image)
            : null;
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    // Add these accessors for backward compatibility
    public function getCountryAttribute()
    {
        return $this->city?->country;
    }

    public function getStateAttribute()
    {
        return $this->city?->state;
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function storeAddress()
    {
        return $this->hasOne(Address::class)->where('user_id', Auth::id());
    }
    public function getLogoUrlAttribute()
    {
        return $this->logo
            ? Storage::url($this->logo)
            : null;
    }

    // Make sure city is always included in serialization
    protected $with = ['city', 'defaultAddress'];

    // public function registerMediaCollections(): void
    // {
    //     $this->addMediaCollection('avatar')
    //         ->acceptsMimeTypes(['image/jpeg', 'image/png'])
    //         ->useDisk('public');
    // }

    public function scopeExcludeUSereAndVendor($query)
    {
        return $query->whereNull('vendor_id')
                     ->whereHas('roles', function ($q) {
                         $q->where('name', '!=', 'user');
                     })->
                     where('id', '!=', Auth::id());
    }

    // public function roles(){
    //     return $this->belongsToMany(Role::class);
    // }

    public function canceledOrders()    
    {
        return $this->hasMany(CanceledOrder::class, 'user_id');
    }

}