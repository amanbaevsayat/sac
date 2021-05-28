<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\PaymentFilter;
use Illuminate\Support\Facades\Auth;

class Payment extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    const QUANTITIES = [
        '1' => '1 месяц',
        '2' => '2 месяца',
        '3' => '3 месяца',
        '6' => '6 месяцев',
    ];

    const ERROR_CODES = [
        0 => 'Оплата успешно проведена',
        5001 => 'Свяжитесь с вашим банком или воспользуйтесь другой картой',  
        5003 => 'Свяжитесь с вашим банком или воспользуйтесь другой картой',
        5004 => 'Свяжитесь с вашим банком или воспользуйтесь другой картой',
        5005 => 'Свяжитесь с вашим банком или воспользуйтесь другой картой',
        5006 => 'Проверьте правильность введенных данных карты или воспользуйтесь другой картой',
        5007 => 'Свяжитесь с вашим банком или воспользуйтесь другой картой',
        5012 => 'Воспользуйтесь другой картой или свяжитесь с банком, выпустившим карту',
        5013 => 'Проверьте корректность суммы',
        5014 => 'Проверьте правильность введенных данных карты или воспользуйтесь другой картой',
        5015 => 'Проверьте правильность введенных данных карты или воспользуйтесь другой картой',
        5019 => 'Свяжитесь с вашим банком или воспользуйтесь другой картой',
        5030 => 'Повторите попытку позже',
        5031 => 'Воспользуйтесь другой картой',
        5033 => 'Свяжитесь с вашим банком или воспользуйтесь другой картой',
        5034 => 'Свяжитесь с вашим банком или воспользуйтесь другой картой',
        5036 => 'Платежи для этой карты запрещены. Попробуйте другую карту',
        5041 => 'Свяжитесь с вашим банком или воспользуйтесь другой картой',
        5043 => 'Свяжитесь с вашим банком или воспользуйтесь другой картой',
        5051 => 'Недостаточно средств на карте',
        5054 => 'Проверьте правильность введенных данных карты или воспользуйтесь другой картой',
        5057 => 'Свяжитесь с вашим банком или воспользуйтесь другой картой',
        5062 => 'Платежи для этой карты запрещены. Попробуйте другую карту',
        5063 => 'Воспользуйтесь другой картой',
        5065 => 'Свяжитесь с вашим банком или воспользуйтесь другой картой',
        5082 => 'Неверно указан код CVV',
        5091 => 'Повторите попытку позже или воспользуйтесь другой картой',
        5092 => 'Повторите попытку позже или воспользуйтесь другой картой',
        5096 => 'Повторите попытку позже',
        5204 => 'Свяжитесь с вашим банком или воспользуйтесь другой картой',
        5206 => 'Свяжитесь с вашим банком или воспользуйтесь другой картой',
        5207 => 'Свяжитесь с вашим банком или воспользуйтесь другой картой',
        5300 => 'Воспользуйтесь другой картой',
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
        'product_id',
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
        'product_bonus_id',
    ];

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

        static::creating(function ($payment) {
            $data = [
                'subscription' => [
                    'renewed' => $payment->data['subscription']['renewed'] ?? false,
                    'from' => $payment->data['subscription']['from'] ?? null,
                    'to' => $payment->data['subscription']['to'] ?? null,
                ],
                'cloudpayments' => $payment->data['cloudpayments'] ?? [],
                'check' => $payment->data['check'] ?? null,
            ];
            $payment->data = $data;
        });

        // auto-sets values on creation
        static::created(function ($payment) {
            if (! $payment->subscription) {
                \Log::error('Отсутствует абонемент. Payment ID: ' . $payment->id);
            }
            $payment->user_id = $payment->subscription->user_id;

            if (isset($payment->type) && isset($payment->subscription_id) && isset($payment->status) && $payment->status == 'Completed') {
                $similarPaymentExists = $payment->subscription->payments()
                    ->where('type', $payment->type)
                    ->where('status', 'Completed')
                    ->get();
                $paymentType = PaymentType::where('product_id', $payment->subscription->product_id)->where('payment_type', $payment->type)->first();
                if (! $paymentType) {
                    \Log::error('Отсутствует тип платежа. Payment ID: ' . $payment->id);
                }
                $type = count($similarPaymentExists) > 0 ? ProductBonus::REPEATED_PAYMENT : ProductBonus::FIRST_PAYMENT;
                $bonus = ProductBonus::where('product_id', $payment->subscription->product_id)
                    ->where('payment_type_id', $paymentType->id)
                    ->where('type', $type)
                    ->active()
                    ->first();

                if (! $bonus) {
                    \Log::error('Отсутствует бонус. Payment ID: ' . $payment->id);
                } else {
                    $payment->product_bonus_id = $bonus->id;
                }
            }
            $payment->save();
        });
        static::deleting(function($payment) {
            UserLog::create([
                'subscription_id' => $payment->subscription_id,
                'user_id' => Auth::id(),
                'type' => UserLog::DELETE_PAYMENT,
                'data' => [
                    'paymentId' => $payment->id,
                ],
            ]);
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
        return $this->belongsTo(Subscription::class, 'subscription_id')->withTrashed();
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
