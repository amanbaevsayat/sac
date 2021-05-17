<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\CustomerFilter;
use App\Filters\ProductFilter;

class Product extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        'code',
        'title',
        'description',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class)->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function usersBonuses()
    {
        return $this->hasMany(UsersBonuses::class, 'product_id');
    }

    public function prices()
    {
        return $this->hasMany(Price::class, 'product_id');
    }

    public function paymentTypes()
    {
        return $this->hasMany(PaymentType::class, 'product_id');
    }

    public function scopeFilter($query, ProductFilter $filters)
    {
        $filters->apply($query);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'product_id');
    }

    public function bonuses()
    {
        return $this->hasMany(Bonus::class, 'product_id');
    }
}
