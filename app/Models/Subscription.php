<?php

namespace App\Models;

use App\Filters\SubscriptionFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    const STATUSES = [
        'tries' => 'Пробует',
        'waiting' => 'Жду оплату',
        'paid' => 'Оплачено',
        'refused' => 'Отказался',
    ];

    const PAYMENT_TYPE = [
        'tries' => 'Пробует бесплатно',
        'transfer' => 'Прямой перевод',
        'cloudpayments' => 'Подписка',
    ];

    protected $fillable = [
        'started_at',
        'paused_at',
        'tries_at',
        'ended_at',
        'product_id',
        'customer_id',
        'price',
        'description',
        'status',
        'payment_type',
        'data',
        'cp_subscription_id',
    ];

    protected $dates = [
        'started_at',
        'paused_at',
        'tries_at',
        'ended_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    protected static function boot() {
        parent::boot();
    
        static::deleting(function($subscription) {
            $subscription->payments()->delete();
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'subscription_id')->latest();
    }

    public function scopeFilter($query, SubscriptionFilter $filters)
    {
        $filters->apply($query);
    }

    public function getEndDate()
    {
        $endedAt = strtotime($this->ended_at ?? $this->tries_at);
        $triesAt = strtotime($this->tries_at ?? $this->ended_at);
        if ($endedAt >= $triesAt) {
            $date = $this->ended_at;
        } else {
            $date = $this->tries_at;
        }
        return $date;
    }

    public function daysLeft()
    {
        $now = time();
        $datediff = strtotime($this->getEndDate()) - $now;

        return round($datediff / (60 * 60 * 24));
    }
}
