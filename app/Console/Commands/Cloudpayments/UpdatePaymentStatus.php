<?php

namespace App\Console\Commands\Cloudpayments;

use App\Models\Card;
use App\Models\Payment;
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
            $id = $item['InvoiceId'];
            $payment = Payment::whereId($item['InvoiceId'])->firstOr(function () use ($id) {
                \Log::info('Платеж не найден. ID: ' . $id);
            });
            if ($payment) {
                // Если статус успешный, то создаем карту и привязываем к платежу
                if ($item['Status'] == 'Completed') {
                    $card = $this->updateOrCreateCard($payment, $item);
                    $payment->update([
                        'card_id' => $card->id,
                    ]);
                }
                $this->updatePayment($payment, $item);
            }
        }
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
