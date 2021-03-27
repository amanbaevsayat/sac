<?php

namespace App\Console\Commands\Statistics;

use App\Models\Product;
use App\Models\Statistics;
use Illuminate\Console\Command;
use App\Models\Subscription;
use Carbon\Carbon;

class UpdateStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда для обновления статистики';

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
        \Log::info('Обновление статистики');

        $products = Product::get();

        foreach ($products as $product) {
            // Страница количественные - 1-диаграмма: Новые лиды
            $this->updateSecondStatistics($product);

            // Страница количественные - 2-диаграмма: Отток клиентов
            $this->updateThirdStatistics($product);

            // Страница количественные - 2-диаграмма: Отток пробных
            $this->updateFourthStatistics($product);
        }
    }

    private function updateSecondStatistics(Product $product)
    {
        $subscriptions = Subscription::
            whereProductId($product->id)
            ->whereHas('payments', function ($q) {
                $q->where('status', 'Completed');
            })->get()
            ->groupBy(function($subscription) {
                $firstCompletePayment = $subscription->payments->where('status', 'Completed')->sortBy('paided_at')->first();

                return (int) Carbon::parse($firstCompletePayment->paided_at)->endOfWeek()->startOfDay()->valueOf(); // grouping by years
            })->toArray();
        
        foreach ($subscriptions as $key => $value) {
            Statistics::updateOrCreate([
                'product_id' => $product->id,
                'type' => Statistics::SECOND_STATISTICS,
                'key' => $key,
            ], [
                'value' => count($value ?? []),
            ]);
        }
    }

    private function updateThirdStatistics(Product $product)
    {
        $subscriptions = Subscription::
            whereProductId($product->id)
            ->whereHas('payments', function ($q) {
                $q->where('status', 'Completed');
            })
            ->whereStatus('refused')
            ->get()
            ->groupBy(function($subscription) {
                return (int) Carbon::parse($subscription->updated_at)->endOfWeek()->startOfDay()->valueOf();
            })->toArray();
        
        foreach ($subscriptions as $key => $value) {
            Statistics::updateOrCreate([
                'product_id' => $product->id,
                'type' => Statistics::THIRD_STATISTICS,
                'key' => $key,
            ], [
                'value' => count($value ?? []),
            ]);
        }
    }

    private function updateFourthStatistics(Product $product)
    {
        $subscriptions = Subscription::
            whereProductId($product->id)
            ->whereDoesntHave('payments', function($q) {
                $q->where('status', 'Completed');
            })
            ->whereStatus('refused')
            ->get()
            ->groupBy(function($subscription) {
                return (int) Carbon::parse($subscription->updated_at)->endOfWeek()->startOfDay()->valueOf(); // grouping by years
            })->toArray();
        
        foreach ($subscriptions as $key => $value) {
            Statistics::updateOrCreate([
                'product_id' => $product->id,
                'type' => Statistics::FOURTH_STATISTICS,
                'key' => $key,
            ], [
                'value' => count($value ?? []),
            ]);
        }
    }
}
