<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    use HasFactory, ModelBase;

    protected $fillable = [
        'title',
        'name',
        'is_active',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function productBonuses()
    {
        return $this->hasMany(ProductBonus::class, 'payment_type_id');
    }
}
