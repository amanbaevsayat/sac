<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'comments',
        'remark_id',
    ];

    public function remark()
    {
        return $this->belongsTo(Remark::class);
    }
}
