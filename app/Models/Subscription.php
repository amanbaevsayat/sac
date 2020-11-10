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
        'trial' => 'Пробная версия',
        'active' => 'Активный',
        'deactive' => 'Неактивен'
    ];

    protected $fillable = [
        'started_at',
        'paused_at',
        'ended_at',
        'product_id',
        'amount',
        'description',
        'status',
        'customer_id',
    ];

    protected $dates = [
        'started_at',
        'paused_at',
        'ended_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeFilter($query, SubscriptionFilter $filters)
    {
        $filters->apply($query);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'subscription_id');
    }
}
