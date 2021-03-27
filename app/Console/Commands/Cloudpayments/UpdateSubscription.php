<?php

namespace App\Console\Commands\Cloudpayments;

use App\Models\Subscription;
use App\Services\CloudPaymentsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class UpdateSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudpayments:update:subscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда для обновления подписок';

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
        $cloudPaymentsService = new CloudPaymentsService();

        $subscriptions = Subscription::whereNotNull('cp_subscription_id')->wherePaymentType('cloudpayments')->get();

        foreach ($subscriptions as $subscription) {
            $response = $cloudPaymentsService->getSubscription($subscription->cp_subscription_id);

            if ($response['Success'] === true) {
                $data = $subscription->data;
                $endedAt = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($response['Model']['NextTransactionDateIso']), 'UTC')->setTimezone('Asia/Almaty');
                $data['cloudpayments'] = $response['Model'];
                $subscription->update([
                    'status' => $subscription->status != 'frozen' ? Subscription::CLOUDPAYMENTS_STATUSES[$response['Model']['Status']] : 'frozen',
                    'data' => $data,
                    'ended_at' => $endedAt,
                ]);
                $subscription->customer->update([
                    'email' => $response['Model']['Email'],
                ]);
            } else {
                \Log::info('Ошибка при поиске подписки. Subscription ID: ' . $subscription->id);
            }
        }
    }
}
