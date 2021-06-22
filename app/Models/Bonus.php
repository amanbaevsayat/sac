<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use HasFactory, ModelBase;

    const PERIOD_TYPE_WEEK = 'week';
    const PERIOD_TYPE_MONTH = 'month';

    const PERIODS = [
        self::PERIOD_TYPE_WEEK => 'По недельно',
        self::PERIOD_TYPE_MONTH => 'По месячно',
    ];

    const HEADERS = [
        'simple_payment-firstPayment' => 'Новые платежи по разовому списанию',
        'simple_payment-repeatedPayment' => 'Повторные платежи по разовому списанию',
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
        'product_id',
        'product_bonus_id',
        'date_type',
        'unix_date',
        'team_id',
        'amount', // Количество платежей
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('stake', 'bonus_amount');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productBonus()
    {
        return $this->belongsTo(ProductBonus::class);
    }
}
