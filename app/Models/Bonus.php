<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use HasFactory, ModelBase;

    const FIRST_PAYMENT = 'firstPayment';
    const REPEATED_PAYMENT = 'repeatedPayment';

    protected $fillable = [
        'product_id',
        'payment_type_id',
        'type',
        'is_active',
        'amount',
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
}
