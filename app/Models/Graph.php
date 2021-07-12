<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Graph extends Model
{
    protected $table = 'graphs';

    protected $fillable = [
        'name',
        'description',
        'color',
        'order',
        'chart_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function statistics()
    {
        return $this->hasMany(StatisticsModel::class, 'graph_id');
    }
}
