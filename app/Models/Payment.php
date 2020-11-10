<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\PaymentFilter;

class Payment extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    const STATUSES = [
        'new' => 'Не оплачено',
        'success' => 'Оплачено',
        'error' => 'Ошибка',
    ];

    protected $fillable = [
        'subscription_id',
        'customer_id',
        'type',
        'slug',
        'amount',
        'status',
        'recurrent', // Для рекуррентных платежей
        'start_date', // Для рекуррентных платежей
        'interval', // Для рекуррентных платежей
        'period', // Для рекуррентных платежей
        'data',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function scopeFilter($query, PaymentFilter $filters)
    {
        $filters->apply($query);
    }
}
