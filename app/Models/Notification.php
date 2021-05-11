<?php

namespace App\Models;

use App\Filters\NotificationFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Notification extends Model
{
    use HasFactory, ModelBase;

    protected $fillable = [
        'type',
        'user_id',
        'payment_id',
        'subscription_id',
        'product_id',
        'in_process',
        'processed',
        'data',
    ];

    const TYPE_CANCEL_SUBSCRIPTION = 1; // Отменили самостоятельно
    const TYPE_SUBSCRIPTION_ERRORS = 2; // Ошибки повторных подписок
    const TYPE_ENDED_SUBSCRIPTIONS_DT = 4; // Закончились абонементы (прямой перевод)
    const TYPE_ENDED_SUBSCRIPTIONS_DT_3 = 5; // Заканчиваются абонементы (прямой перевод) До +3 дней
    const TYPE_ENDED_TRIAL_PERIOD = 6; // Закончился пробный период
    const WAITING_PAYMENT_CP = 7; // Жду оплату по подписке

    const TYPES = [
        self::TYPE_CANCEL_SUBSCRIPTION => 'Отменили подписку',
        self::TYPE_SUBSCRIPTION_ERRORS => 'Ошибки подписок',
        self::WAITING_PAYMENT_CP => 'Жду оплату по подписке',
        self::TYPE_ENDED_SUBSCRIPTIONS_DT => 'Закончились абонементы (прям. перевод)',
        self::TYPE_ENDED_SUBSCRIPTIONS_DT_3 => 'Заканчиваются абонементы (прям. перевод)',
        self::TYPE_ENDED_TRIAL_PERIOD => 'Закончился пробный период',
    ];

    const NOTIFICATION_TYPES = [
        self::TYPE_CANCEL_SUBSCRIPTION,
        self::TYPE_SUBSCRIPTION_ERRORS,
        self::WAITING_PAYMENT_CP,
        self::TYPE_ENDED_SUBSCRIPTIONS_DT,
        self::TYPE_ENDED_SUBSCRIPTIONS_DT_3,
        self::TYPE_ENDED_TRIAL_PERIOD,
    ];

    protected $casts = [
        'data' => 'array'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function scopeFilter($query, NotificationFilter $filters)
    {
        $filters->apply($query);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
