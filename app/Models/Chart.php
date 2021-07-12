<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chart extends Model
{
    const TYPE_QUANTITATIVE = 1;
    const TYPE_FINANCIAL = 2;

    protected $table = 'charts';

    protected $fillable = [
        'title',
        'description',
        'order',
        'is_stacking',
        'type',
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
