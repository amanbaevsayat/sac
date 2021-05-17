<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersBonuses extends Model
{
    use HasFactory, ModelBase;

    const DATE_TYPES = [
        'week',
        'month',
    ];

    protected $fillable = [
        'user_ids', // json (array)
        'product_id',
        'bonus_id',
        'date_type',
        'unix_date',
        'amount',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'user_ids' => 'array',
    ];
}
