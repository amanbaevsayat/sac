<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\PaymentFilter;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    use HasFactory, SoftDeletes, ModelBase, LogsActivity;

    const QUANTITIES = [
        '1' => '1 месяц',
        '2' => '2 месяца',
        '3' => '3 месяца',
        '6' => '6 месяцев',
    ];

    const CLOUDPAYMENTS_OPERATION_STATUSES = [
        'AwaitingAuthentication' => 'AwaitingAuthentication', // Ожидает аутентификации
        'Authorized' => 'Authorized', // Авторизована
        'Completed' => 'Completed', // Завершена
        'Cancelled' => 'Cancelled', // Отменена
        'Declined' => 'Declined', // Отклонена
    ];

    const CLOUDPAYMENTS_SUBSCRIPTION_STATUSES = [
        'Active' => 'Active', // Подписка активна
        'PastDue' => 'PastDue', // Просрочена
        'Cancelled' => 'Cancelled', // Отменена
        'Rejected' => 'Rejected', // Отклонена
        'Expired' => 'Expired', // Завершена
    ];

    const CLOUDPAYMENTS_INTERVALS = [
        'Day',
        'Week',
        'Month',
    ];

    const CLOUDPAYMENTS_PERIODS = [
        1,
        2
    ];

    const STATUSES = [
        'new' => 'Не оплачено',
        'Completed' => 'Оплачено',
        'Declined' => 'Ошибка',
        'Frozen' => 'Заморозка',
    ];

    protected $fillable = [
        'id',
        'subscription_id',
        'card_id',
        'customer_id',
        'transaction_id',
        'user_id',
        'quantity',
        'type',
        'amount',
        'status',
        'paided_at',
        'data',
        // 'slug',
        // 'recurrent', // Для рекуррентных платежей
        // 'start_date', // Для рекуррентных платежей
        // 'interval', // Для рекуррентных платежей
        // 'period', // Для рекуррентных платежей
    ];

    protected static $logAttributes = [
        'subscription_id',
        'card_id',
        'customer_id',
        'transaction_id',
        'user_id',
        'quantity',
        'type',
        'amount',
        'status',
        'paided_at',
        'data',
        // 'slug',
        // 'recurrent', // Для рекуррентных платежей
        // 'start_date', // Для рекуррентных платежей
        // 'interval', // Для рекуррентных платежей
        // 'period', // Для рекуррентных платежей
    ];

    protected static $ignoreChangedAttributes = [];

    protected $dates = [
        'start_date',
        'paided_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        // auto-sets values on creation
        static::creating(function ($query) {
            $data = [
                'subscription' => [
                    'renewed' => $query->data['subscription']['renewed'] ?? false,
                    'from' => $query->data['subscription']['from'] ?? null,
                    'to' => $query->data['subscription']['to'] ?? null,
                ],
                'cloudpayments' => $query->data['cloudpayments'] ?? [],
                'check' => $query->data['check'] ?? null,
            ];
            $query->data = $data;
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function scopeFilter($query, PaymentFilter $filters)
    {
        $filters->apply($query);
    }
}
