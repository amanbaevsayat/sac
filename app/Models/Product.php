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

    public function reasons()
    {
        return $this->hasMany(Reason::class, 'product_id')->where('is_active', 1);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('stake', 'employment_at');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    public function prices()
    {
        return $this->hasMany(Price::class, 'product_id');
    }

    public function paymentTypes()
    {
        return $this->belongsToMany(PaymentType::class);
            // ->withPivot('stake', 'employment_at');
    }

    public function scopeFilter($query, ProductFilter $filters)
    {
        $filters->apply($query);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'product_id');
    }

    public function productBonuses()
    {
        return $this->hasMany(ProductBonus::class, 'product_id');
    }

    public function bonuses()
    {
        return $this->hasMany(Bonus::class, 'product_id');
    }
}
