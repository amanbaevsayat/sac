<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Filters\UserLogFilter;

class UserLog extends Model
{
    use HasFactory, ModelBase;

    const END_DATE = 1;             // 1) Дата окончания
    const SUBSCRIPTION_STATUS = 2;  // 2) Статус абонемента
    const CP_UNSUBSCRIBE = 3;       // 3) Запрос в cloudpayments об отказе подписки
    const CP_NEXT_PAYMENT_DATE = 4; // 4) Запрос в cloudpayments об изменении даты следующего платежа
    const CP_AUTO_RENEWAL = 5;      // 5) Автопродление абонемента (дата окончания)
    const PAYMENT_TYPE = 6;         // 6) Тип оплаты абонемента
    const DELETE_PAYMENT = 7;       // 7) Удаление платежа абонемента
    const DELETE_SUBSCRIPTION = 8;  // 8) Удаление абонемента
    const CUSTOMER_PHONE = 9;       // 9) Телефон абонемента
    const START_DATE = 10;          // 10) Дата старта абонемента
    const CHANGE_SUBSCRIPTION_USER  = 11; // 11) Оператор абонемента
    const MANUAL_WRITE_OFF          = 12; // 12) Кнопка ручное списание с подписки

    const TYPES = [
        self::END_DATE              => 'Дата окончания',
        self::START_DATE            => 'Дата старта',
        self::SUBSCRIPTION_STATUS   => 'Статус абонемента',
        self::CP_UNSUBSCRIBE        => 'Запрос в cloudpayments об отказе подписки',
        self::CP_NEXT_PAYMENT_DATE  => 'Запрос в cloudpayments об изменении даты следующего платежа',
        self::CP_AUTO_RENEWAL       => 'Автопродление абонемента (дата окончания)',
        self::PAYMENT_TYPE          => 'Тип оплаты абонемента',
        self::DELETE_PAYMENT        => 'Удаление платежа',
        self::DELETE_SUBSCRIPTION   => 'Удаление абонемента',
        self::CUSTOMER_PHONE        => 'Телефон абонемента',
        self::CHANGE_SUBSCRIPTION_USER  => 'Оператор абонемента',
        self::MANUAL_WRITE_OFF      => 'Ручное списание',
    ];

    protected $fillable = [
        'subscription_id',
        'customer_id',
        'user_id',
        'type',
        'data',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeFilter($query, UserLogFilter $filters)
    {
        $filters->apply($query);
    }

    public function getDescription()
    {
        switch ($this->type) {
            case self::END_DATE:
                $message = '<b style="color: red">Старая запись: </b>' . ($this->data['old'] ? strftime('%d %b %Y(%H:%M)', (new \DateTime($this->data['old']))->getTimestamp()) : null) . '<br> <b style="color: green">Новая запись: </b>' . ($this->data['new'] ? strftime('%d %b %Y(%H:%M)', (new \DateTime($this->data['new']))->getTimestamp()) : null);
                return $message;
                break;
            case self::START_DATE:
                $message = '<b style="color: red">Старая запись: </b>' . ($this->data['old'] ? strftime('%d %b %Y(%H:%M)', (new \DateTime($this->data['old']))->getTimestamp()) : null) . '<br> <b style="color: green">Новая запись: </b>' . ($this->data['new'] ? strftime('%d %b %Y(%H:%M)', (new \DateTime($this->data['new']))->getTimestamp()) : null);
                return $message;
                break;
            case self::SUBSCRIPTION_STATUS:
                $message = '<b style="color: red">Старая запись: </b>' . (Subscription::STATUSES[$this->data['old']] ?? null) . '<br> <b style="color: green">Новая запись: </b>' . (Subscription::STATUSES[$this->data['new']] ?? null);
                return $message;
                break;
            case self::CP_UNSUBSCRIBE:
                break;
            case self::CP_NEXT_PAYMENT_DATE:
                $message = '<b style="color: red">Старая запись: </b>' . ($this->data['old'] ? strftime('%d %b %Y(%H:%M)', (new \DateTime($this->data['old']))->getTimestamp()) : null) . '<br> <b style="color: green">Новая запись: </b>' . ($this->data['new'] ? strftime('%d %b %Y(%H:%M)', (new \DateTime($this->data['new']))->getTimestamp()) : null);
                return $message;
                break;
            case self::CP_AUTO_RENEWAL:
                $message = '<b style="color: red">Старая запись: </b>' . ($this->data['old'] ? strftime('%d %b %Y(%H:%M)', (new \DateTime($this->data['old']))->getTimestamp()) : null) . '<br> <b style="color: green">Новая запись: </b>' . ($this->data['new'] ? strftime('%d %b %Y(%H:%M)', (new \DateTime($this->data['new']))->getTimestamp()) : null);
                return $message;
                break;
            case self::PAYMENT_TYPE:
                $message = '<b style="color: red">Старая запись: </b>' . (Subscription::PAYMENT_TYPE[$this->data['old']] ?? null) . '<br> <b style="color: green">Новая запись: </b>' . (Subscription::PAYMENT_TYPE[$this->data['new']] ?? null);
                return $message;
                break;
            case self::DELETE_PAYMENT:
                return '<b>ID платежа: </b>' . ($this->data['paymentId'] ?? null);
                break;
            case self::DELETE_SUBSCRIPTION:
                return null;
                break;
            case self::CUSTOMER_PHONE:
                $message = '<b style="color: red">Старая запись: </b>' . $this->data['old'] . '<br> <b style="color: green">Новая запись: </b>' . $this->data['new'];
                return $message;
                break;
            case self::CHANGE_SUBSCRIPTION_USER:
                $message = '<b style="color: red">Старая запись: </b>' . $this->data['old'] . '<br> <b style="color: green">Новая запись: </b>' . $this->data['new'];
                return $message;
                break;
            case self::MANUAL_WRITE_OFF:
                break;
            default:
                # code...
                break;
        }
    }
}
