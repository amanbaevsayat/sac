<?php

namespace App\Console\Commands\Cloudpayments;

use App\Models\Card;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\CloudPaymentsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

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
        foreach ($cpPayments as $item) {
            $paymentId = $item['InvoiceId'];
            $payment = Payment::whereId($paymentId)->first();
            if (!$payment) {
                $subscription = Subscription::where('cp_subscription_id', $item['SubscriptionId'])->first();
                
                if ($subscription) {
                    $payment = $subscription->payments()->create([
                        'subscription_id' => $subscription->id,
                        'customer_id' => $subscription->customer->id,
                        'quantity' => 1,
                        'type' => $subscription->payment_type,
                        'slug' => Str::uuid(),
                        'status' => $item->Status,
                        'amount' => $item->Amount,
                        'recurrent' => 1,
                        'start_date' => $subscription->started_at ?? null,
                        'interval' => 'Month' ?? null,
                        'period' => 1 ?? null,
                        'paided_at' => $item->ConfirmDateIso,
                        'data' => [
                            'cloudpayments' => $item,
                        ],
                    ]);
                } else {
                    \Log::info('Платеж с cloudpayments не найден. TransactionId: ' . $item['TransactionId']);
                    continue;
                }
            }

            // Если статус успешный, то создаем карту и привязываем к платежу
            if ($item['Status'] == 'Completed') {
                $card = $this->updateOrCreateCard($payment, $item);
                $payment->update([
                    'card_id' => $card->id,
                ]);
                $payment->subscription()->update([
                    'status' => 'paid' ?? null,
                ]);
            }
            $this->updatePayment($payment, $item);
        }

        \Log::info('Start - UpdatePaymentStatus');
    }

    private function updateOrCreateCard(Payment $payment, $item): Card
    {
        return Card::updateOrCreate(
            [
                'token' => $item['Token'],
                'subscription_id' => $payment->subscription_id,
                'customer_id' => $payment->customer_id,
            ],
            [
                'first_six' => $item['CardFirstSix'],
                'last_four' => $item['CardLastFour'],
                'exp_date' => $item['CardExpDate'],
                'type' => $item['CardType'],
                'name' => $item['Name'],
            ]
        );
    }

    private function updatePayment(Payment $payment, array $item)
    {
        $data = $payment->data ?? [];
        $data['cloudpayments'] = $item;
        $payment->update([
            'status' => $item['Status'],
            'data' => $data,
        ]);

        $payment->subscription()->update([
            'cp_subscription_id' => $item['SubscriptionId'] ?? null,
        ]);
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
