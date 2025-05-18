<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileConfirm extends Model
{
    public $table = 'mobile_confirm';

    public $fillable = [
        'mobile',
        'user_id',
        'dialling_code',
        'code',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'mobile' => 'string',
        'dialling_code' => 'string',
        'code' => 'string',
        'created_at' => 'datetime:Y-m-d h:i A',

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'nullable',
        'dialling_code' => 'required',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
