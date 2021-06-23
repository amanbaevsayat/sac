<?php

namespace App\Console\Commands\Cloudpayments;

use App\Models\Card;
use App\Models\Customer;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\CloudPaymentsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class UpdateNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда для обновления уведомлении';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now()->addDays(2)->toDateString();
        $threeDaysAhead = Carbon::now()->addDays(6)->toDateString();

        $secondTypePayments = Payment::where('type', 'cloudpayments')->whereHas('subscription', function ($q) {
            $q->where('status', '!=', 'refused');
        })->whereStatus('Declined')->get();

        foreach ($secondTypePayments as $payment) {
            Notification::updateOrCreate([
                'type' => Notification::TYPE_SUBSCRIPTION_ERRORS,
                'subscription_id' => $payment->subscription->id,
                'payment_id' => $payment->id,
            ], [
                'product_id' => $payment->subscription->product->id,
                'data' => [
                    'error_description' => $payment->data['cloudpayments']['CardHolderMessage'] ?? null,
                    'paided_at' => $payment->paided_at,
                ],
            ]);
        }

        $fourthTypeSubscriptions = Subscription::whereIn('status', ['waiting', 'paid', 'tries'])->wherePaymentType('transfer')->whereDate('ended_at', '<', $now)->get();
        $fourthTypeSubscriptionsIds = [];
        foreach ($fourthTypeSubscriptions as $subscription) {
            $fourthTypeSubscriptionsIds[] = $subscription->id;
            Notification::updateOrCreate([
                'type' => Notification::TYPE_ENDED_SUBSCRIPTIONS_DT,
                'subscription_id' => $subscription->id,
            ], [
                'product_id' => $subscription->product->id,
                'data' => [],
            ]);
        }
        Notification::where('type', Notification::TYPE_ENDED_SUBSCRIPTIONS_DT)->whereNotIn('subscription_id', $fourthTypeSubscriptionsIds)->delete();

        $fifthTypeSubscriptions = Subscription::whereIn('status', ['waiting', 'paid', 'tries'])->wherePaymentType('transfer')->whereBetween('ended_at', [$now, $threeDaysAhead])->get();
        $fifthTypeSubscriptionsIds = [];
        foreach ($fifthTypeSubscriptions as $subscription) {
            $fifthTypeSubscriptionsIds[] = $subscription->id;
            Notification::updateOrCreate([
                'type' => Notification::TYPE_ENDED_SUBSCRIPTIONS_DT_3,
                'subscription_id' => $subscription->id,
            ], [
                'product_id' => $subscription->product->id,
                'data' => [],
            ]);
        }

        Notification::where('type', Notification::TYPE_ENDED_SUBSCRIPTIONS_DT_3)->whereNotIn('subscription_id', $fifthTypeSubscriptionsIds)->delete();

        $sixthTypeSubscriptions = Subscription::whereIn('status', ['tries', 'waiting'])->wherePaymentType('tries')->whereRaw('CASE WHEN tries_at > ended_at THEN tries_at ELSE ended_at END < ?', [$threeDaysAhead])->get();
        $sixthTypeSubscriptionsIds = [];
        foreach ($sixthTypeSubscriptions as $subscription) {
            $sixthTypeSubscriptionsIds[] = $subscription->id;
            Notification::updateOrCreate([
                'type' => Notification::TYPE_ENDED_TRIAL_PERIOD,
                'subscription_id' => $subscription->id,
            ], [
                'product_id' => $subscription->product->id,
                'data' => [],
            ]);
        }
        Notification::where('type', Notification::TYPE_ENDED_TRIAL_PERIOD)->whereNotIn('subscription_id', $sixthTypeSubscriptionsIds)->delete();

        $seventhTypeSubscriptions = Subscription::whereIn('status', ['waiting', 'rejected'])->wherePaymentType('cloudpayments')->get();
        $seventhTypeSubscriptionsIds = [];
        foreach ($seventhTypeSubscriptions as $subscription) {
            $seventhTypeSubscriptionsIds[] = $subscription->id;
            Notification::updateOrCreate([
                'type' => Notification::WAITING_PAYMENT_CP,
                'subscription_id' => $subscription->id,
            ], [
                'product_id' => $subscription->product->id,
                'data' => [],
            ]);
        }
        Notification::where('type', Notification::WAITING_PAYMENT_CP)->whereNotIn('subscription_id', $seventhTypeSubscriptionsIds)->delete();
    }
}
