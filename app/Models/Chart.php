<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chart extends Model
{
    protected $table = 'charts';

    protected $fillable = [
        'title',
        'description',
        'order',
        'is_stacking',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function graphs()
    {
        return $this->hasMany(Graph::class, 'chart_id')->orderBy('order');
    }
}
