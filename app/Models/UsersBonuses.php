<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersBonuses extends Model
{
    use HasFactory, ModelBase;

    const PERIOD_TYPE_WEEK = 'week';
    const PERIOD_TYPE_MONTH = 'month';

    const PERIODS = [
        self::PERIOD_TYPE_WEEK => 'По недельно',
        self::PERIOD_TYPE_MONTH => 'По месячно',
    ];

    const HEADERS = [
        'transfer-firstPayment' => 'Новые платежи по прямому переводу',
        'transfer-repeatedPayment' => 'Повторные платежи по прямому переводу',
        'cloudpayments-firstPayment' => 'Новые платежи по подписке',
        'cloudpayments-repeatedPayment' => 'Повторные платежи по подписке',
    ];

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
