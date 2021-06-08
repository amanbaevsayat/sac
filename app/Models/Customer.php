<?php

namespace App\Models;

use App\Filters\CustomerFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Customer extends Model
{
    use HasFactory, ModelBase, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'comments',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    protected $dates = ['deleted_at'];

    protected static function boot() {
        parent::boot();
    
        static::updating(function ($customer) {
            if ($customer->getOriginal('phone') != $customer->phone) {
                UserLog::create([
                    'subscription_id' => null,
                    'customer_id' => $customer->id,
                    'user_id' => Auth::id(),
                    'type' => UserLog::CUSTOMER_PHONE,
                    'data' => [
                        'old' => $customer->getOriginal('phone'),
                        'new' => $customer->phone,
                    ],
                ]);
            }
        });

        static::deleting(function($customer) {
            $customer->subscriptions()->delete();
            $customer->payments()->delete();
        });

        // static::restoring(function($customer) {
        //     $customer->subscriptions()->withTrashed()->where('deleted_at', '>=', $customer->deleted_at)->restore();
        //     $customer->payments()->withTrashed()->where('deleted_at', '>=', $customer->deleted_at)->restore();
        // });

        // static::restored(function($customer) {
        //     $customer->subscriptions()->withTrashed()->where('deleted_at', '>=', $customer->deleted_at)->restore();
        //     $customer->payments()->withTrashed()->where('deleted_at', '>=', $customer->deleted_at)->restore();
        // });
    }

    public function getNameWithPhoneAttribute()
    {
        return "{$this->name} {$this->phone}";
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'customer_id');
    }

    public function cards()
    {
        return $this->hasMany(Card::class, 'customer_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'customer_id');
    }

    public function scopeFilter($query, CustomerFilter $filters)
    {
        $filters->apply($query);
    }
}
