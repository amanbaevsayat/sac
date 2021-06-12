<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        'customer_id',
        'cp_account_id',
        'first_six',
        'last_four',
        'exp_date',
        'type',
        'name',
        'token',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
