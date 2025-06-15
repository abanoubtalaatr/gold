<?php

namespace App\Models;

use App\Models\Log;
use Spatie\Permission\Models\Role as SpatieRole;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends SpatieRole
{

    // public $translatedAttributes = ['name'];
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'guard_name',
        'is_active',
        'vendor_id',
        'name_ar',
    ];

    public function roles(){
        return $this->hasMany(Role::class, 'vendor_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(Log::class, 'by_user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSuperAdminRoles($query)
    {
        return $query->whereNull('vendor_id')
                     ->whereHas('roles', function ($q) {
                         $q->where('name', '!=', 'user');
                     });
    }
}
