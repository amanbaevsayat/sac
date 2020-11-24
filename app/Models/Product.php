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
        'price',
        'trial_price',
    ];

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class)->withTimestamps();
    }

    public function prices()
    {
        return $this->hasMany(Price::class, 'product_id');
    }

    public function scopeFilter($query, ProductFilter $filters)
    {
        $filters->apply($query);
    }
}
