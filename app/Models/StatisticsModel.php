<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatisticsModel extends Model
{
    protected $table = 'statistics';
    // use HasFactory, ModelBase;

    // Страница: Количественные
    // Диаграмма: Новые лиды
    // График: Новые лиды (Instagram)
    const FIRST_STATISTICS = 1;

    // Страница: Количественные
    // Диаграмма: Динамика базы клиентов (по факту)
    // График: Новые платежи
    const SECOND_STATISTICS = 2;

    // Страница: Количественные
    // Диаграмма: Динамика базы клиентов (по факту)
    // График: Отток клиентов
    const THIRD_STATISTICS = 3;

    // Страница: Количественные
    // Диаграмма: Динамика базы клиентов (по факту)
    // График: Отток пробных
    const FOURTH_STATISTICS = 4;

    const FIFTH_STATISTICS = 5; // DEPRECATED
    const SIXTH_STATISTICS = 6; // DEPRECATED
    
    // Страница: Количественные
    // Диаграмма: Динамика базы клиентов (по дате старта) - Показатели с задержкой в 2 недели
    // График: Отток пробных
    const SEVENTH_STATISTICS = 7;

    // Страница: Количественные
    // Диаграмма: Динамика базы клиентов (по дате старта) - Показатели с задержкой в 2 недели
    // График: Отток клиентов
    const EIGHTH_STATISTICS = 8;

    // Страница: Количественные
    // Диаграмма: Динамика базы клиентов (по дате старта) - Показатели с задержкой в 2 недели
    // График: Приток клиентов
    const NINTH_STATISTICS = 9;

    // Страница: Количественные
    // Диаграмма: Оплата второго месяца - Показатели с задержкой в 2 недели
    // График: Купили второй абонемент.
    const TENTH_STATISTICS = 10;

    // Страница: Количественные
    // Диаграмма: Оплата второго месяца - Показатели с задержкой в 2 недели
    // График: Есть один платеж, но отказались
    const ELEVENTH_STATISTICS = 11;

    // Страница: Количественные
    // Диаграмма: Активные абонементы по типу оплаты
    // График: Cloudpayments (подписка)
    const TWELFTH_STATISTICS = 12;

    // Страница: Количественные
    // Диаграмма: Активные абонементы по типу оплаты
    // График: Прямой перевод
    const FIFTEENTH_STATISTICS = 15;

    // Страница: Количественные
    // Диаграмма: Новые лиды
    // График: Подключились в WhatsApp
    const THIRTEENTH_STATISTICS = 13;

    // Страница: Количественные
    // Диаграмма: События недели
    // График: События недели
    const EVENTS_OF_WEEK = 140;

    // Страница: Количественные
    // Диаграмма: Активные абонементы (общее)
    // График: Общее
    const SIXTEENTH_STATISTICS = 16;

    // Страница: Количественные
    // Диаграмма: Рентабельность услуги
    // График: Общий оборот
    const SEVENTEENTH_STATISTICS = 17;

    const PERIOD_TYPE_WEEK = 'week';
    const PERIOD_TYPE_MONTH = 'month';

    const PERIODS = [
        self::PERIOD_TYPE_WEEK => 'По недельно',
        self::PERIOD_TYPE_MONTH => 'По месячно',
    ];

    protected $fillable = [
        'period_type',
        'graph_id',
        'key',
        'value',
        'product_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
