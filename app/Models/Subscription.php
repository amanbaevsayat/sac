<?php

namespace App\Models;

use App\Filters\SubscriptionFilter;
use App\Services\CloudPaymentsService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class Subscription extends Model
{
    use HasFactory, SoftDeletes, ModelBase;

    const CLOUDPAYMENTS_STATUSES = [
        'Active' => 'paid',
        'PastDue' => 'paid',
        'Cancelled' => 'refused',
        'Rejected' => 'rejected',
        'Expired' => 'refused',
    ];

    const STATUSES = [
        'tries' => 'Пробует',
        'waiting' => 'Жду оплату',
        'paid' => 'Оплачено',
        'rejected' => 'Отклонена (3 раза)',
        'refused' => 'Отказался',
        // 'frozen' => 'Заморожен',
    ];

    const PAYMENT_TYPE = [
        'tries' => 'Пробует бесплатно',
        'transfer' => 'Прямой перевод',
        'cloudpayments' => 'Подписка',
        'simple_payment' => 'Разовое списание',
    ];

    protected $fillable = [
        'started_at',
        'paused_at',
        'tries_at',
        'frozen_at',
        'defrozen_at',
        'ended_at',
        'product_id',
        'customer_id',
        'price',
        'description',
        'status',
        'payment_type',
        'data',
        'cp_subscription_id',
        'user_id',
        'manual_write_off_at',
        'reason_id',
    ];

    protected $dates = [
        'started_at',
        'paused_at',
        'tries_at',
        'ended_at',
        'created_at',
        'updated_at',
        'manual_write_off_at',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    protected static function boot() {
        parent::boot();

        static::creating(function ($query) {
            $data = [
                'cloudpayments' => $query->data['cloudpayments'] ?? [],
            ];
            $query->data = $data;
        });

        static::updating(function ($subscription) {
            $oldEndedAt = Carbon::parse($subscription->getOriginal('ended_at') ?? null);
            $endedAt = Carbon::parse($subscription->ended_at);
            $isEqualTwoEndedAt = Carbon::parse($subscription->getOriginal('ended_at') ?? null)->format('Y-m-d') == Carbon::parse($subscription->ended_at ?? null)->format('Y-m-d');
            $isEqualTwoStartedAt = Carbon::parse($subscription->getOriginal('started_at') ?? null)->format('Y-m-d') == Carbon::parse($subscription->started_at ?? null)->format('Y-m-d');
            if (! $isEqualTwoEndedAt) {
                if ($subscription->status != 'refused' && $subscription->payment_type == 'cloudpayments' && isset($subscription->cp_subscription_id)) {
                    // Если дата окончания меньше now, 
                    // то будет двойное списание, 
                    // потому что дата окончания заднее число и cloudpayments попытается снять еще.
                    if (Carbon::parse($endedAt)->gt(Carbon::now())) {
                        $cloudPaymentsService = new CloudPaymentsService();
                        $data = [
                            'Id' => $subscription->cp_subscription_id,
                            'StartDate' => Carbon::parse($endedAt)->format('Y-m-d\TH:i:s.u'),
                        ];

                        // Запрос в cloudpayments об изменении даты следующего платежа
                        $response = $cloudPaymentsService->updateSubscription($data);

                        UserLog::create([
                            'subscription_id' => $subscription->id,
                            'user_id' => Auth::id(),
                            'type' => UserLog::CP_NEXT_PAYMENT_DATE,
                            'data' => [
                                'old' => $oldEndedAt,
                                'new' => Carbon::parse($endedAt),
                                'request' => $data,
                                'response' => $response,
                            ],
                        ]);
                    }
                } else {
                    UserLog::create([
                        'subscription_id' => $subscription->id,
                        'user_id' => Auth::id(),
                        'type' => UserLog::END_DATE,
                        'data' => [
                            'old' => $oldEndedAt,
                            'new' => $endedAt,
                        ],
                    ]);
                }
            }

            if (! $isEqualTwoStartedAt) {
                UserLog::create([
                    'subscription_id' => $subscription->id,
                    'user_id' => Auth::id(),
                    'type' => UserLog::START_DATE,
                    'data' => [
                        'old' => Carbon::parse($subscription->getOriginal('started_at') ?? null)->format('Y-m-d'),
                        'new' => Carbon::parse($subscription->started_at ?? null)->format('Y-m-d'),
                    ],
                ]);
            }

            if ($subscription->status == 'refused') {
                $subscription->cancelCPSubscription();
            }

            if ($subscription->getOriginal('status') != $subscription->status) {
                UserLog::create([
                    'subscription_id' => $subscription->id,
                    'user_id' => Auth::id(),
                    'type' => UserLog::SUBSCRIPTION_STATUS,
                    'data' => [
                        'old' => $subscription->getOriginal('status'),
                        'new' => $subscription->status,
                    ],
                ]);
            }

            if ($subscription->getOriginal('user_id') != $subscription->user_id) {
                $oldUser = User::find($subscription->getOriginal('user_id'));
                $newUser = User::find($subscription->user_id);
                UserLog::create([
                    'subscription_id' => $subscription->id,
                    'user_id' => Auth::id(),
                    'type' => UserLog::CHANGE_SUBSCRIPTION_USER,
                    'data' => [
                        'old' => $oldUser->account ?? $subscription->getOriginal('user_id'),
                        'new' => $newUser->account ?? $subscription->user_id,
                    ],
                ]);
            }

            if ($subscription->getOriginal('payment_type') != $subscription->payment_type) {
                UserLog::create([
                    'subscription_id' => $subscription->id,
                    'user_id' => Auth::id(),
                    'type' => UserLog::PAYMENT_TYPE,
                    'data' => [
                        'old' => $subscription->getOriginal('payment_type'),
                        'new' => $subscription->payment_type,
                    ],
                ]);

                if ($subscription->getOriginal('payment_type') == 'cloudpayments') {
                    $subscription->cancelCPSubscription();
                }
            }
        });
    
        static::deleting(function($subscription) {
            UserLog::create([
                'subscription_id' => $subscription->id,
                'user_id' => Auth::id(),
                'type' => UserLog::DELETE_SUBSCRIPTION,
                'data' => [],
            ]);
            $subscription->cancelCPSubscription();
            $subscription->payments()->delete();
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function reason()
    {
        return $this->belongsTo(Reason::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'subscription_id')->latest();
    }

    public function scopeFilter($query, SubscriptionFilter $filters)
    {
        $filters->apply($query);
    }

    /**
     * Клиенты
     *
     * @param $query
     * @return void
     */
    public function scopeClient($query)
    {
        return $query->whereHas('payments', function (Builder $q) {
            $q->where('status', 'Completed');
        }, '>=', 1);
    }

    /**
     * Пробные
     *
     * @param $query
     * @return void
     */
    public function scopeTrial($query)
    {
        return $query->whereDoesntHave('payments', function (Builder $q) {
            $q->where('status', 'Completed');
        });
    }

    /**
     * Должники
     *
     * @param $query
     * @return void
     */
    public function scopeDeptors($query)
    {
        $now = Carbon::now()->format('Y-m-d 00:00:00');

        return $query->where('ended_at', '<=', $now)
            ->where('status', '!=', 'refused');
    }

    public function getEndDate()
    {
        $endedAt = strtotime($this->ended_at ?? $this->tries_at);
        $triesAt = strtotime($this->tries_at ?? $this->ended_at);
        if ($endedAt >= $triesAt) {
            $date = $this->ended_at;
        } else {
            $date = $this->tries_at;
        }
        return $date;
    }

    public function daysLeft()
    {
        $now = time();
        $datediff = strtotime($this->getEndDate()) - $now;

        return round($datediff / (60 * 60 * 24));
    }

    /**
     * Отмена подписки в Cloudpayments
     *
     * @return void
     */
    public function cancelCPSubscription()
    {
        if ($this->cp_subscription_id) {
            $cloudPaymentsService = new CloudPaymentsService();
            $response = $cloudPaymentsService->cancelSubscription($this->cp_subscription_id);
            UserLog::create([
                'subscription_id' => $this->id,
                'user_id' => Auth::id(),
                'type' => UserLog::CP_UNSUBSCRIBE,
                'data' => [
                    'request' => $this->cp_subscription_id,
                    'response' => $response,
                ],
            ]);

            $this->update([
                'cp_subscription_id' => null,
            ]);
        }
    }
}
