<?php

namespace App\Models\Traits;

trait IsActive
{

    public function scopeActive($query, $is_active = 1)
    {
        if ($is_active == 0) {
            return $query->where('is_active', 0);
        }

        return $query->where('is_active', 1);
    }

    public function getIsActiveLabelAttribute()
    {
        if ($this->is_active == 1)
            return __('admin.yes');

        return __('admin.no');
    }


    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->is_active??false;
    }
}
