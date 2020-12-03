<?php

namespace App\Console\Commands\Dump;

use App\Models\Card;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LoadOldDataToDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'load:old-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда для загрузки старых данных в базу';

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
        Artisan::call('migrate:fresh');
        Artisan::call('db:seed');

        $path = public_path('dshpyrk3_sa-28-11-2020-01-16.json');
        $json = json_decode(file_get_contents($path));
        foreach ($json as $table) {
            if ($table->type == 'table') {
                if ($table->name == 'customers') {
                    $this->updateCustomers($table->data);
                }

                if ($table->name == 'subscriptions') {
                    $this->updateSubscriptions($table->data);
                }

                if ($table->name == 'transactions') {
                    $this->updatePayments($table->data);
                }
            }
        }
    }

    private function updateCustomers(array $data = [])
    {
        $phones = [];
        foreach ($data as $item) {
            if (!in_array($item->phone, $phones)) {
                $customer = Customer::create([
                    'name' => $item->name,
                    'phone' => $item->phone,
                    'email' => $item->email,
                    'data' => [
                        'old' => $item
                    ],
                ]);

                if (!$item->subscription_id) {
                    $product = Product::inRandomOrder()->first();

                    Subscription::create([
                        'started_at' => $customer->data['old']['start_date'],
                        'paused_at' => null,
                        'ended_at' => $customer->data['old']['end_date'],
                        'product_id' => $product->id,
                        'customer_id' => $customer->id,
                        'price' => 3000,
                        'description' => '',
                        'status' => $this->getStatus($item->remark_id),
                        'payment_type' => ($customer->data['old']['subscription_type_id'] == 2) ? 'cloudpayments' : 'transfer', // TODO Пересмотреть
                    ]);
                }

                $phones[] = $item->phone;
            }
        }
    }

    private function getStatus(int $id)
    {
        $statuses = [
            1 => 'paid',
            5 => 'refused',
            6 => 'tries',
            7 => 'waiting',
        ];

        return $statuses[$id];
    }

    private function updateSubscriptions(array $data = [])
    {
        foreach ($data as $item) {
            $customer = Customer::where('data->old->subscription_id', $item->id)->first();
            $product = Product::inRandomOrder()->first();
            if ($customer) {
                Subscription::create([
                    'started_at' => $customer->data['old']['start_date'],
                    'paused_at' => null,
                    'ended_at' => $customer->data['old']['end_date'],
                    'product_id' => $product->id,
                    'customer_id' => $customer->id,
                    'price' => $item->Amount,
                    'description' => $item->Description,
                    'status' => $item->Status,
                    'payment_type' => ($customer->data['old']['subscription_type_id'] == 2) ? 'cloudpayments' : 'transfer',
                    'data' => [
                        'cloudpayments' => $item,
                    ],
                ]);
            }
        }
    }

    private function updatePayments(array $data = [])
    {
        foreach ($data as $item) {
            $subscription = Subscription::where('data->cloudpayments->id', $item->subscription_id)->first();
            if ($subscription) {
                $card = $this->updateOrCreateCard($subscription, $item);

                Payment::create([
                    'subscription_id' => $subscription->id,
                    'card_id' => $card->id,
                    'customer_id' => $subscription->customer->id,
                    'quantity' => 1,
                    'type' => $subscription->payment_type,
                    'slug' => Str::uuid(),
                    'status' => $item->Status,
                    'amount' => $item->Amount,
                    'recurrent' => 1,
                    'start_date' => $subscription['data']['cloudpayments']['StartDateIso'] ?? null,
                    'interval' => $subscription['data']['cloudpayments']['Interval'] ?? null,
                    'period' => $subscription['data']['cloudpayments']['Period'] ?? null,
                    'paided_at' => $item->updated_at,
                    'data' => [
                        'cloudpayments' => $item,
                    ],
                ]);
            }
        }
    }

    private function updateOrCreateCard(Subscription $subscription, $item): Card
    {
        return Card::updateOrCreate(
            [
                'token' => $item->Token,
                'subscription_id' => $subscription->id,
                'customer_id' => $subscription->customer_id,
            ],
            [
                'first_six' => $item->CardFirstSix,
                'last_four' => $item->CardLastFour,
                'exp_date' => $item->CardExpDate,
                'type' => $item->CardType,
                'name' => $item->Name,
            ]
        );
    }
}
