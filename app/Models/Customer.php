<?php

namespace App\Models;

use App\Filters\CustomerFilter;
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

    public function getNameWithPhoneAttribute()
    {
        return "{$this->name} {$this->phone}";
    }

    public function remark()
    {
        return $this->belongsTo(Remark::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'customer_id');
    }

    public function scopeFilter($query, CustomerFilter $filters)
    {
        $filters->apply($query);
    }
}
