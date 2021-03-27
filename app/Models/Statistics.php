<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statistics extends Model
{
    use HasFactory, ModelBase;

    // Страница количественные - 1-диаграмма: Новые лиды (INSTAGRAM)
    const FIRST_STATISTICS = 1;

    // Страница количественные - 1-диаграмма: Новые лиды
    const SECOND_STATISTICS = 2;

    // Страница количественные - 2-диаграмма: Отток клиентов
    const THIRD_STATISTICS = 3;

    // Страница количественные - 2-диаграмма: Отток пробных
    const FOURTH_STATISTICS = 4;
    const FIFTH_STATISTICS = 5;
    const SIXTH_STATISTICS = 6;

    protected $fillable = [
        'type',
        'key',
        'value',
        'product_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
