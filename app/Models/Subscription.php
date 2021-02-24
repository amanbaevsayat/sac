<?php

namespace App\Models;

use App\Filters\SubscriptionFilter;
use App\Services\CloudPaymentsService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
class Subscription extends Model
{
    use HasFactory, SoftDeletes, ModelBase, LogsActivity;

    const CLOUDPAYMENTS_STATUSES = [
        'Active' => 'paid',
        'PastDue' => 'waiting',
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
        'frozen' => 'Заморожен',
    ];

    const PAYMENT_TYPE = [
        'tries' => 'Пробует бесплатно',
        'transfer' => 'Прямой перевод',
        'cloudpayments' => 'Подписка',
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
    ];

    protected static $logAttributes = [
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
    ];

    protected static $ignoreChangedAttributes = [];

    protected static $logOnlyDirty = true;

    protected $dates = [
        'started_at',
        'paused_at',
        'tries_at',
        'ended_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    protected static function boot() {
        parent::boot();

        // auto-sets values on creation
        static::creating(function ($query) {
            $data = [
                'cloudpayments' => $query->data['cloudpayments'] ?? [],
            ];
            $query->data = $data;
        });
    
        static::deleting(function($subscription) {
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
            $cloudPaymentsService->cancelSubscription($this->cp_subscription_id);
        }
    }
}
