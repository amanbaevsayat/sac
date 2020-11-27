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
        'paid' => 'Оплачено',
        'refused' => 'Отказался',
        'tries' => 'Пробует',
        'waiting' => 'Жду оплату',
    ];

    const PAYMENT_TYPE = [
        'tries' => 'Пробная версия',
        'cloudpayments' => 'Подписка',
        'transfer' => 'Прямой перевод',
    ];

    protected $fillable = [
        'started_at',
        'paused_at',
        'ended_at',
        'product_id',
        'customer_id',
        'price',
        'description',
        'status',
        'payment_type',
        'data',
    ];

    protected $dates = [
        'started_at',
        'paused_at',
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
        return $this->hasMany(Payment::class, 'subscription_id');
    }

    public function scopeFilter($query, SubscriptionFilter $filters)
    {
        $filters->apply($query);
    }

    public function daysLeft()
    {
        if (!$this->ended_at) return '';
        $now = time();
        $end_date = strtotime($this->ended_at);
        $datediff = $end_date - $now;

        return round($datediff / (60 * 60 * 24));
    }
}
