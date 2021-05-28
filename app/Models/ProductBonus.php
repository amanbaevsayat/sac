<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBonus extends Model
{
    use HasFactory, ModelBase;

    const FIRST_PAYMENT = 'firstPayment';
    const REPEATED_PAYMENT = 'repeatedPayment';

    protected $fillable = [
        'product_id',
        'payment_type_id',
        'type',
        'is_active',
        'amount', // Бонус за один платеж
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @param $query
     * @return static
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(ProductBonus::class);
    }
}
