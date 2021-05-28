<?php

namespace App\Models;

use App\Filters\UserFilter;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account',
        'name',
        'email',
        'phone',
        'role_id',
        'pass',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function getRole()
    {
        return $this->role->code;
    }

    public function isOperator()
    {
        return $this->role->code == "operator";
    }

    public function isHead()
    {
        return $this->role->code == "head";
    }

    public function isHost()
    {
        return $this->role->code == "host";
    }

    public function scopeFilter($query, UserFilter $filters)
    {
        $filters->apply($query);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('stake', 'employment_at');
    }

    public function bonuses()
    {
        return $this->belongsToMany(Bonus::class)->withPivot('stake', 'bonus_amount');
    }
}
