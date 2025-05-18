<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

protected $fillable = [
        'key',
        'value',
        'group',
        'creatable_type',
        'creatable_id',
        'type',
    ];

    public static function boot()
    {
        parent::boot();

        Setting::updated(function ($setting) {

            $settings = Setting::all();

            foreach ($settings as $record) {
                Cache::delete($record->key);
                Cache::forever($record->key, $record->value);
            }

        });
    }

    public static function getFromCache($keys = [])
    {

        if (count($keys) == 0) {

            $settings = Setting::all();
            $keys = $settings->pluck('key')->toArray();

            if (Cache::get($keys[0]) == null) {
                foreach ($settings as $record) {
                    Cache::delete($record->key);
                    Cache::forever($record->key, $record->value);
                }
            }

            return Cache::getMultiple($keys);
        }

        return Cache::getMultiple($keys);
    }

}