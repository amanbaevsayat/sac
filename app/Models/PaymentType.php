<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    use HasFactory, ModelBase;

    protected $fillable = [
        'product_id',
        'payment_type',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function bonuses()
    {
        return $this->hasMany(Bonus::class, 'payment_type_id');
    }
}
