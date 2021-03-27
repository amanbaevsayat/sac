<?php

namespace App\Models;

use App\Filters\CustomerFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Customer extends Model
{
    use HasFactory, ModelBase, LogsActivity;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'comments',
        'data',
    ];

    protected static $logAttributes = [
        'name',
        'phone',
        'email',
        'comments',
    ];

    protected static $ignoreChangedAttributes = [];

    protected static $logOnlyDirty = true;

    protected $casts = [
        'data' => 'array',
    ];

    protected $dates = ['deleted_at'];

    protected static function boot() {
        parent::boot();
    
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

    public function payments()
    {
        return $this->hasMany(Payment::class, 'customer_id');
    }

    public function scopeFilter($query, CustomerFilter $filters)
    {
        $filters->apply($query);
    }
}
