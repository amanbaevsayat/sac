<?php

namespace App\Console\Commands\Cloudpayments;

use App\Models\Card;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\CloudPaymentsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class UpdatePaymentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudpayments:update:payment_status {--date=}'; // Date example: 2020-12-31

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда для обновления статуса платежей';

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

        $cpPayments = $cloudPaymentsService->getTransactions($this->getDate());
        $ids = [];
        foreach ($cpPayments['Model'] as $item) {
            $payment = Payment::whereId($item['InvoiceId'])->first();
            $subscription = $payment->subscription ?? null;

            if (!isset($subscription)) {
                $subscription = Subscription::whereNotNull('cp_subscription_id')->where('cp_subscription_id', $item['SubscriptionId'])->first();

                if (!isset($subscription)) {
                    if (is_string($item['JsonData'])) {
                        $jsonData = json_decode($item['JsonData']);
                        if (json_last_error() == JSON_ERROR_NONE) {
                            if (isset($jsonData->cloudPayments->recurrent->SubscriptionId)) {
                                $id = $jsonData->cloudPayments->recurrent->SubscriptionId;
                                $subscription = Subscription::whereId($id)->first();
                            }
                        }
                    }
                }
            }

            if (!isset($subscription)) {
                \Log::info('Subscription is null. TransactionId: ' . $item['TransactionId']);
                continue;
            }

            if (in_array($item['InvoiceId'], $ids)) {
                \Log::info('Платеж дублируется. TransactionId: ' . $item['TransactionId']);

                $payment = $subscription->payments()->updateOrCreate([
                    'transaction_id' => $item['TransactionId'],
                ], [
                    'subscription_id' => $subscription->id,
                    'customer_id' => $subscription->customer->id,
                    'quantity' => 1,
                    'type' => $subscription->payment_type,
                    'status' => $item['Status'],
                    'amount' => $item['Amount'],
                    'paided_at' => $item['CreatedDateIso'] ?? $item['AuthDateIso'],
                    'data' => [
                        'cloudpayments' => $item,
                    ],
                ]);
            }

            if (is_null($item['InvoiceId'])) {
                $payment = $subscription->payments()->updateOrCreate([
                    'transaction_id' => $item['TransactionId'],
                ], [
                    'customer_id' => $subscription->customer_id,
                    'user_id' => null,
                    'type' => 'cloudpayments',
                    'status' => $item['Status'],
                    'amount' => $item['Amount'],
                    'paided_at' => Carbon::now(),
                ]);
                \Log::info('InvoiceId is null. TransactionId: ' . $item['TransactionId']);
            }

            if (!isset($payment)) {
                \Log::info('Payment is null. TransactionId: ' . $item['TransactionId']);
                continue;
            }
            $this->updatePayment($payment, $item);

            // Если статус успешный, то создаем карту для платежа
            if ($item['Status'] == 'Completed') {
                // $this->updateOrCreateCard($payment, $item);

                // Если у платежа есть SubscriptionId, значит он рекуррентный
                if ($item['SubscriptionId']) {
                    // Проверяем, есть ли метка у платежа, чтобы можно было продлить абонемент
                    $data = $payment->data ?? [];
                    if ((isset($data['subscription']) && $data['subscription']['renewed'] == false)) {
                        $endedAt = $payment->subscription->ended_at;
                        $data['subscription'] = [
                            'renewed' => true,
                            'from' => $endedAt,
                            'to' => Carbon::parse($endedAt)->addMonths(1),
                        ];
                    } else {
                        if (isset($data['subscription'])) {
                            $data['subscription']['renewed'] == false;
                        } else {
                            $endedAt = $payment->subscription->ended_at;
                            $data['subscription'] = [
                                'renewed' => false,
                                'from' => $endedAt,
                                'to' => Carbon::parse($endedAt)->addMonths(1),
                            ];
                        }
                    }

                    $payment->update([
                        'data' => $data
                    ]);
                }
            } else if ($item['Status'] == 'Authorized' && $item['ReasonCode'] == 0) {
                \Log::info('Status = Authorized, ReasonCode = 0. TransactionId: ' . $item['TransactionId']);
                // $cloudPaymentsService = new CloudPaymentsService();
                // $response = $cloudPaymentsService->paymentsConfirm([
                //     'TransactionId' => $item['TransactionId'],
                //     'Amount' => $item['Amount'],
                // ]);
                // if (isset($response["Success"]) && $response["Success"] == false) {
                //     \Log::info('Ошибка при подтверждение оплаты. TransactionId: ' . $item['TransactionId'] . '. Message: ' . ($data['Message'] ?? null));
                //     continue;
                // }
            }

            $ids[] = $item['InvoiceId'];
        }
    }

    private function updateOrCreateCard(Payment $payment, $item)
    {
        if ($item['Status'] == 'Completed') {
            $payment->card()->updateOrCreate(
                [
                    'token' => $item['Token'],
                    'subscription_id' => $payment->subscription_id,
                    'customer_id' => $payment->customer_id,
                ],
                [
                    'customer_id' => $payment->customer_id,
                    'subscription_id' => $payment->subscription_id,
                    'token' => $item['Token'],
                    'first_six' => $item['CardFirstSix'],
                    'last_four' => $item['CardLastFour'],
                    'exp_date' => $item['CardExpDate'],
                    'type' => $item['CardType'],
                    'name' => $item['Name'],
                ]
            );
        }
    }

    private function updatePayment(Payment $payment, array $item)
    {
        $data = $payment->data ?? [];
        $data['cloudpayments'] = $item;
        $payment->update([
            'transaction_id' => $item['TransactionId'],
            'status' => $item['Status'],
            'paided_at' => $item['CreatedDateIso'] ?? $item['AuthDateIso'],
            'data' => $data,
        ]);
        if ($item['SubscriptionId'] && !empty($item['SubscriptionId'])) {
            $payment->subscription()->update([
                'cp_subscription_id' => $item['SubscriptionId'],
            ]);
        }
    }

    /**
     * @return string
     */
    public function getDate()
    {
        if ($this->option('date')) {
            return Carbon::parse($this->option('date'))->format('Y-m-d');
        }

        return Carbon::now()->format('Y-m-d');
    }
}
