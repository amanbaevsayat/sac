<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatisticsModel extends Model
{
    protected $table = 'statistics';
    // use HasFactory, ModelBase;

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
    
    // Страница количественные - 3-диаграмма: Отток пробных
    const SEVENTH_STATISTICS = 7;

    // Страница количественные - 3-диаграмма: Отток клиентов
    const EIGHTH_STATISTICS = 8;

    // Страница количественные - 3-диаграмма: Отток клиентов
    const NINTH_STATISTICS = 9;

    // Страница количественные - 4-диаграмма: Купили второй абонемент
    const TENTH_STATISTICS = 10;

    // Страница количественные - 4-диаграмма: Есть один платеж, но отказались.
    const ELEVENTH_STATISTICS = 11;

    // Страница количественные - 5-диаграмма: Активные абонементы.
    const TWELFTH_STATISTICS = 12;

    const PERIOD_TYPE_WEEK = 'week';
    const PERIOD_TYPE_MONTH = 'month';

    const PERIODS = [
        self::PERIOD_TYPE_WEEK => 'По недельно',
        self::PERIOD_TYPE_MONTH => 'По месячно',
    ];

    protected $fillable = [
        'period_type',
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
