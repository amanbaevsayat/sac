<?php

namespace App\Console\Commands\Statistics;

use App\Models\Product;
use App\Models\StatisticsModel;
use Illuminate\Console\Command;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

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

        $periods = [
            StatisticsModel::PERIOD_TYPE_WEEK,
            StatisticsModel::PERIOD_TYPE_MONTH,
        ];

        foreach ($periods as $period) {
            foreach ($products as $product) {
                // Страница количественные - 1-диаграмма: Новые лиды
                $this->updateSecondStatistics($product, $period);
    
                // Страница количественные - 2-диаграмма: Отток клиентов
                $this->updateThirdStatistics($product, $period);
    
                // Страница количественные - 2-диаграмма: Отток пробных
                $this->updateFourthStatistics($product, $period);

                // Страница количественные - 3-диаграмма: Отток пробных
                $this->updateFifthStatistics($product, $period);

                // Страница количественные - 3-диаграмма: Отток клиентов
                $this->updateSeventhStatistics($product, $period);

                // Страница количественные - 3-диаграмма: Приток клиентов
                $this->updateEighthStatistics($product, $period);

                // Страница количественные - 4-диаграмма: Купили второй абонемент
                $this->updateTenthStatistics($product, $period);

                // Страница количественные - 4-диаграмма: Есть один платеж, но отказались.
                $this->updateEleventhStatistics($product, $period);

                // Страница количественные - 5-диаграмма: Активные абонементы. - Cloudpayments
                $this->updateTwelfthStatistics($product, $period);

                // Страница количественные - 1-диаграмма: Подключились в Whatsapp.
                $this->updateThirteenthStatistics($product, $period);

                // Страница количественные - 5-диаграмма: Активные абонементы. - Прямой перевод
                $this->updateFifteenthStatistics($product, $period);
            }
        }
    }

    private function updateSecondStatistics(Product $product, string $period)
    {
        $subscriptions = Subscription::whereProductId($product->id)
            ->whereHas('payments', function ($q) {
                $q->where('status', 'Completed');
            })->get()
            ->groupBy(function($subscription) use ($period) {
                $firstCompletePayment = $subscription->payments->where('status', 'Completed')->sortBy('paided_at')->first();
                if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                    return (int) Carbon::parse($firstCompletePayment->paided_at)->endOfMonth()->startOfDay()->valueOf();
                } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                    return (int) Carbon::parse($firstCompletePayment->paided_at)->endOfWeek()->startOfDay()->valueOf();
                }
            })->toArray();
        foreach ($subscriptions as $key => $value) {
            StatisticsModel::updateOrCreate([
                'period_type' => $period,
                'type' => StatisticsModel::SECOND_STATISTICS,
                'product_id' => $product->id,
                'key' => $key,
            ], [
                'value' => count($value ?? []),
            ]);
        }
    }

    private function updateThirdStatistics(Product $product, string $period)
    {
        $subscriptions = Subscription::
            whereProductId($product->id)
            ->whereHas('payments', function ($q) {
                $q->where('status', 'Completed');
            })
            ->whereStatus('refused')
            ->get()
            ->groupBy(function($subscription) use ($period) {
                if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                    return (int) Carbon::parse($subscription->updated_at)->endOfMonth()->startOfDay()->valueOf();
                } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                    return (int) Carbon::parse($subscription->updated_at)->endOfWeek()->startOfDay()->valueOf();
                }
            })->toArray();
        
        foreach ($subscriptions as $key => $value) {
            StatisticsModel::updateOrCreate([
                'period_type' => $period,
                'type' => StatisticsModel::THIRD_STATISTICS,
                'product_id' => $product->id,
                'key' => $key,
            ], [
                'value' => count($value ?? []),
            ]);
        }
    }

    private function updateFourthStatistics(Product $product, string $period)
    {
        $subscriptions = Subscription::
            whereProductId($product->id)
            ->whereDoesntHave('payments', function($q) {
                $q->where('status', 'Completed');
            })
            ->whereStatus('refused')
            ->get()
            ->groupBy(function($subscription) use ($period) {
                if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                    return (int) Carbon::parse($subscription->updated_at)->endOfMonth()->startOfDay()->valueOf();
                } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                    return (int) Carbon::parse($subscription->updated_at)->endOfWeek()->startOfDay()->valueOf();
                }
            })->toArray();
        
        foreach ($subscriptions as $key => $value) {
            StatisticsModel::updateOrCreate([
                'period_type' => $period,
                'product_id' => $product->id,
                'type' => StatisticsModel::FOURTH_STATISTICS,
                'key' => $key,
            ], [
                'value' => count($value ?? []),
            ]);
        }
    }

    private function updateFifthStatistics(Product $product, string $period)
    {
        $subscriptions = Subscription::
            whereProductId($product->id)
            ->whereDoesntHave('payments', function($q) {
                $q->where('status', 'Completed');
            })
            ->whereStatus('refused')
            ->get()
            ->groupBy(function($subscription) use ($period) {
                if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                    return (int) Carbon::parse($subscription->started_at)->endOfMonth()->startOfDay()->valueOf();
                } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                    return (int) Carbon::parse($subscription->started_at)->endOfWeek()->startOfDay()->valueOf();
                }
            })->toArray();
        
        foreach ($subscriptions as $key => $value) {
            StatisticsModel::updateOrCreate([
                'period_type' => $period,
                'product_id' => $product->id,
                'type' => StatisticsModel::SEVENTH_STATISTICS,
                'key' => $key,
            ], [
                'value' => count($value ?? []),
            ]);
        }
    }

    private function updateSeventhStatistics(Product $product, string $period)
    {
        $subscriptions = Subscription::
            whereProductId($product->id)
            ->whereHas('payments', function ($q) {
                $q->where('status', 'Completed');
            })
            ->whereStatus('refused')
            ->get()
            ->groupBy(function($subscription) use ($period) {
                if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                    return (int) Carbon::parse($subscription->started_at)->endOfMonth()->startOfDay()->valueOf();
                } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                    return (int) Carbon::parse($subscription->started_at)->endOfWeek()->startOfDay()->valueOf();
                }
            })->toArray();
        
        foreach ($subscriptions as $key => $value) {
            StatisticsModel::updateOrCreate([
                'period_type' => $period,
                'type' => StatisticsModel::EIGHTH_STATISTICS,
                'product_id' => $product->id,
                'key' => $key,
            ], [
                'value' => count($value ?? []),
            ]);
        }
    }

    private function updateEighthStatistics(Product $product, string $period)
    {
        $subscriptions = Subscription::whereProductId($product->id)
            ->whereStatus('paid')
            ->whereHas('payments', function ($q) {
                $q->where('status', 'Completed');
            })->get()
            ->groupBy(function($subscription) use ($period) {
                if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                    return (int) Carbon::parse($subscription->started_at)->endOfMonth()->startOfDay()->valueOf();
                } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                    return (int) Carbon::parse($subscription->started_at)->endOfWeek()->startOfDay()->valueOf();
                }
            })->toArray();
        foreach ($subscriptions as $key => $value) {
            StatisticsModel::updateOrCreate([
                'period_type' => $period,
                'type' => StatisticsModel::NINTH_STATISTICS,
                'product_id' => $product->id,
                'key' => $key,
            ], [
                'value' => count($value ?? []),
            ]);
        }
    }

    private function updateTenthStatistics(Product $product, string $period)
    {
        $subscriptions = Subscription::
            whereProductId($product->id)
            ->whereHas('payments', function (Builder $query) {
                $query->where('status', 'Completed');
            }, '=', 2)
            // ->whereStatus('refused')
            ->get()
            ->groupBy(function($subscription) use ($period) {
                $lastPayment = $subscription->payments()->latest()->first();
                if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                    return (int) Carbon::parse($lastPayment->paided_at)->endOfMonth()->startOfDay()->valueOf();
                } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                    return (int) Carbon::parse($lastPayment->paided_at)->endOfWeek()->startOfDay()->valueOf();
                }
            })->toArray();
        
        foreach ($subscriptions as $key => $value) {
            StatisticsModel::updateOrCreate([
                'period_type' => $period,
                'type' => StatisticsModel::TENTH_STATISTICS,
                'product_id' => $product->id,
                'key' => $key,
            ], [
                'value' => count($value ?? []),
            ]);
        }
    }

    private function updateEleventhStatistics(Product $product, string $period)
    {
        $subscriptions = Subscription::whereProductId($product->id)
            ->whereStatus('refused')
            ->whereHas('payments', function (Builder $query) {
                $query->where('status', 'Completed');
            }, '=', 1)
            ->get()
            ->groupBy(function($subscription) use ($period) {
                if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                    return (int) Carbon::parse($subscription->updated_at)->endOfMonth()->startOfDay()->valueOf();
                } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                    return (int) Carbon::parse($subscription->updated_at)->endOfWeek()->startOfDay()->valueOf();
                }
            })->toArray();
        foreach ($subscriptions as $key => $value) {
            StatisticsModel::updateOrCreate([
                'period_type' => $period,
                'type' => StatisticsModel::ELEVENTH_STATISTICS,
                'product_id' => $product->id,
                'key' => $key,
            ], [
                'value' => count($value ?? []),
            ]);
        }
    }

    private function updateTwelfthStatistics(Product $product, string $period)
    {
        if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
            $date = (int) Carbon::now()->endOfMonth()->startOfDay()->valueOf();
        } else {
            $date = (int) Carbon::now()->endOfWeek()->startOfDay()->valueOf();
        }
        $subscriptionsCount = Subscription::whereProductId($product->id)
            ->where('payment_type', 'cloudpayments')
            ->whereIn('status', ['paid', 'waiting'])
            ->whereIn('payment_type', ['transfer', 'cloudpayments'])
            ->whereHas('payments', function ($q) {
                $q->where('status', 'Completed');
            })
            ->count();

        
        StatisticsModel::updateOrCreate([
            'period_type' => $period,
            'type' => StatisticsModel::TWELFTH_STATISTICS,
            'product_id' => $product->id,
            'key' => $date,
        ], [
            'value' => $subscriptionsCount,
        ]);
    }

    private function updateThirteenthStatistics(Product $product, string $period)
    {
        $subscriptions = Subscription::
            whereProductId($product->id)
            ->get()
            ->groupBy(function($subscription) use ($period) {
                if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                    return (int) Carbon::parse($subscription->started_at)->endOfMonth()->startOfDay()->valueOf();
                } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                    return (int) Carbon::parse($subscription->started_at)->endOfWeek()->startOfDay()->valueOf();
                }
            })->toArray();
        foreach ($subscriptions as $key => $value) {
            StatisticsModel::updateOrCreate([
                'period_type' => $period,
                'product_id' => $product->id,
                'type' => StatisticsModel::THIRTEENTH_STATISTICS,
                'key' => $key,
            ], [
                'value' => count($value ?? []),
            ]);
        }
    }

    private function updateFifteenthStatistics(Product $product, string $period)
    {
        if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
            $date = (int) Carbon::now()->endOfMonth()->startOfDay()->valueOf();
        } else {
            $date = (int) Carbon::now()->endOfWeek()->startOfDay()->valueOf();
        }
        $subscriptionsCount = Subscription::whereProductId($product->id)
            ->where('payment_type', 'transfer')
            ->whereIn('status', ['paid', 'waiting'])
            ->whereIn('payment_type', ['transfer', 'cloudpayments'])
            ->whereHas('payments', function ($q) {
                $q->where('status', 'Completed');
            })
            ->count();

        
        StatisticsModel::updateOrCreate([
            'period_type' => $period,
            'type' => StatisticsModel::FIFTEENTH_STATISTICS,
            'product_id' => $product->id,
            'key' => $date,
        ], [
            'value' => $subscriptionsCount,
        ]);
    }
}
