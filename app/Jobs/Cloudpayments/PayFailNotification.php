<?php

namespace App\Jobs\Cloudpayments;

use App\Exceptions\NoticeException;
use App\Models\CpNotification;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\UserLog;
use Throwable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PayFailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The podcast instance.
     *
     * @var array
     */
    protected $data;

    /**
     * Create a new job instance.
     *
     * @param array $data
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            CpNotification::updateOrCreate([
                'type' => CpNotification::PAY_FAIL,
                'transaction_id' => $this->data['TransactionId'],
            ], [
                'type' => CpNotification::PAY_FAIL,
                'transaction_id' => $this->data['TransactionId'],
                'request' => (array) $this->data,
            ]);

            \DB::beginTransaction();

            if (Payment::where('transaction_id', $this->data['TransactionId'])->exists()) {
                throw new \Exception('Данная транзакция уже существует. Transaction ID: ' . $this->data['TransactionId']);
            }

            $subscription = Subscription::where('cp_subscription_id', $this->data['SubscriptionId'])->whereNotNull('cp_subscription_id')->first();

            if (! isset($subscription)) {
                $jsonData = '';
                if (isset($this->data['Data']) && is_string($this->data['Data'])) {
                    $jsonData = json_decode($this->data['Data']);
                }
                if (isset($jsonData->subscription->id)) {
                    $subscription = Subscription::whereId($jsonData->subscription->id)->first();
                }

                if (! isset($subscription)) {
                    $subscription = Subscription::whereId($this->data['AccountId'])->first();
                }
            }

            if (! isset($subscription)) {
                throw new NoticeException('Не найден абонемент. Transaction Id: ' . $this->data['TransactionId']);
            }

            if (! isset($subscription->customer)) {
                throw new NoticeException('Не найден клиент. Transaction Id: ' . $this->data['TransactionId']);
            }

            $subscriptionData = [
                'from' => null,
                'to' => null,
            ];

            $updateData = [];
            if ($this->data['Status'] == 'Completed') {
                $updateData = [
                    'status' => 'paid',
                ];
                if ($this->data['SubscriptionId']) {
                    $subscriptionData = [
                        'from' => Carbon::createFromFormat('Y-m-d H:i:s', $subscription->ended_at, 'Asia/Almaty'),
                        'to' => Carbon::createFromFormat('Y-m-d H:i:s', $subscription->ended_at, 'Asia/Almaty')->addMonths(1),
                    ];
        
                    $oldEndedAt = Carbon::createFromFormat('Y-m-d H:i:s', $subscription->ended_at, 'Asia/Almaty');
                    $newEndedAt = Carbon::createFromFormat('Y-m-d H:i:s', $subscription->ended_at, 'Asia/Almaty')->addMonths(1);
                    UserLog::create([
                        'subscription_id' => $subscription->id,
                        'user_id' => Auth::id() ?? null,
                        'type' => UserLog::CP_AUTO_RENEWAL,
                        'data' => [
                            'old' => $oldEndedAt,
                            'new' => $newEndedAt,
                            'request' => $this->data,
                        ],
                    ]);
                    $updateData['cp_subscription_id'] = $this->data['SubscriptionId'];
                    $updateData['ended_at'] = $newEndedAt;
                }

                $subscription->update($updateData);
                $this->updateOrCreateCard($subscription->customer);
            }

            $this->data['CardHolderMessage'] = Payment::ERROR_CODES[$this->data['ReasonCode'] ?? 0];

            $payment = Payment::updateOrCreate([
                'transaction_id' => $this->data['TransactionId'],
            ], [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'product_id' => $subscription->product->id,
                'customer_id' => $subscription->customer->id,
                'quantity' => 1,
                'type' => $subscription->payment_type,
                'status' => $this->data['Status'],
                'amount' => $this->data['Amount'],
                'paided_at' => Carbon::createFromFormat('Y-m-d H:i:s', $this->data['DateTime'], 'UTC')->setTimezone('Asia/Almaty'),
                'data' => [
                    'cloudpayments' => $this->data,
                    'subscription' => $subscriptionData,
                ],
            ]);

            if (! isset($payment)) {
                throw new NoticeException('Не создался платеж. Transaction Id: ' . $this->data['TransactionId']);
            }
            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('PayFailNotification. TransactionID: ' . $this->data['TransactionId']);

            report($e);
        }
    }

    private function updateOrCreateCard(Customer $customer)
    {
        $customer->cards()->updateOrCreate([
            'token' => $this->data['Token'],
            'cp_account_id' => $this->data['AccountId'],
        ],
        [
            'cp_account_id' => $this->data['AccountId'],
            'token' => $this->data['Token'],
            'first_six' => $this->data['CardFirstSix'] ?? null,
            'last_four' => $this->data['CardLastFour'] ?? null,
            'exp_date' => $this->data['CardExpDate'] ?? null,
            'type' => $this->data['CardType'] ?? null,
            'name' => $this->data['Name'] ?? 'Default',

        ]);
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        // Send user notification of failure, etc...
    }
}
