<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Filters\ProductFilter;
use App\Filters\TeamFilter;

class Team extends Model
{
    use HasFactory, ModelBase;

    protected $fillable = [
        'name',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('stake', 'employment_at');
    }

    public function scopeFilter($query, TeamFilter $filters)
    {
        $filters->apply($query);
    }
}
