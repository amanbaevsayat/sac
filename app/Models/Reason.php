<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    use HasFactory, ModelBase;

    protected $table = 'reasons';
    protected $fillable = [
        'product_id',
        'title',
        'is_active',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class, 'reason_id');
    }
}
