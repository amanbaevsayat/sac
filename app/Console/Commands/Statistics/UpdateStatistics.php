<?php

namespace App\Console\Commands\Statistics;

use App\Models\Graph;
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
    protected $signature = 'update:statistics {period}';

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
        $this->period = $this->argument('period');

        $graph = Graph::whereType(StatisticsModel::SECOND_STATISTICS)->first();
        if (isset($graph)) {
            $productIds = $graph->products->pluck('id')->toArray();
            if (! empty($productIds)) {
                $this->updateSecondStatistics($graph, $productIds);
            }
        }

        $graph = Graph::whereType(StatisticsModel::THIRD_STATISTICS)->first();
        if (isset($graph)) {
            $productIds = $graph->products->pluck('id')->toArray();
            if (! empty($productIds)) {
                $this->updateThirdStatistics($graph, $productIds);
            }
        }

        $graph = Graph::whereType(StatisticsModel::FOURTH_STATISTICS)->first();
        if (isset($graph)) {
            $productIds = $graph->products->pluck('id')->toArray();
            if (! empty($productIds)) {
                $this->updateFourthStatistics($graph, $productIds);
            }
        }

        $graph = Graph::whereType(StatisticsModel::SEVENTH_STATISTICS)->first();
        if (isset($graph)) {
            $productIds = $graph->products->pluck('id')->toArray() ?? [];
            if (! empty($productIds)) {
                $this->updateSeventhStatistics($graph, $productIds);
            }
        }

        $graph = Graph::whereType(StatisticsModel::EIGHTH_STATISTICS)->first();
        if (isset($graph)) {
            $productIds = $graph->products->pluck('id')->toArray();
            if (! empty($productIds)) {
                $this->updateEighthStatistics($graph, $productIds);
            }
        }

        $graph = Graph::whereType(StatisticsModel::NINTH_STATISTICS)->first();
        if (isset($graph)) {
            $productIds = $graph->products->pluck('id')->toArray();
            if (! empty($productIds)) {
                $this->updateNinthStatistics($graph, $productIds);
            }
        }

        $graph = Graph::whereType(StatisticsModel::TENTH_STATISTICS)->first();
        if (isset($graph)) {
            $productIds = $graph->products->pluck('id')->toArray();
            if (! empty($productIds)) {
                $this->updateTenthStatistics($graph, $productIds);
            }
        }

        $graph = Graph::whereType(StatisticsModel::ELEVENTH_STATISTICS)->first();
        if (isset($graph)) {
            $productIds = $graph->products->pluck('id')->toArray();
            if (! empty($productIds)) {
                $this->updateEleventhStatistics($graph, $productIds);
            }
        }

        $graph = Graph::whereType(StatisticsModel::TWELFTH_STATISTICS)->first();
        if (isset($graph)) {
            $productIds = $graph->products->pluck('id')->toArray();
            if (! empty($productIds)) {
                $this->updateTwelfthStatistics($graph, $productIds);
            }
        }

        $graph = Graph::whereType(StatisticsModel::THIRTEENTH_STATISTICS)->first();
        if (isset($graph)) {
            $productIds = $graph->products->pluck('id')->toArray();
            if (! empty($productIds)) {
                $this->updateThirteenthStatistics($graph, $productIds);
            }
        }

        $graph = Graph::whereType(StatisticsModel::FIFTEENTH_STATISTICS)->first();
        if (isset($graph)) {
            $productIds = $graph->products->pluck('id')->toArray();
            if (! empty($productIds)) {
                $this->updateFifteenthStatistics($graph, $productIds);
            }
        }

        $graph = Graph::whereType(StatisticsModel::SIXTEENTH_STATISTICS)->first();
        if (isset($graph)) {
            $productIds = $graph->products->pluck('id')->toArray();
            if (! empty($productIds)) {
                $this->updateSixteenthStatistics($graph, $productIds);
            }
        }

        // $graph = Graph::whereType(StatisticsModel::SEVENTEENTH_STATISTICS)->first();
        // if (isset($graph)) {
        //     $productIds = $graph->products->pluck('id')->toArray();
        //     if (! empty($productIds)) {
        //         $this->updateSeventeenthStatistics($graph, $productIds);
        //     }
        // }
    }

    private function updateSecondStatistics(Graph $graph, array $productIds)
    {
        $period = $this->period;

        $products = Subscription::whereIn('product_id', $productIds)->whereHas('payments', function ($q) {
                $q->where('status', 'Completed');
            })->get()
            ->groupBy('product_id')
            ->transform(function($items, $k) use ($period) {
                return $items->groupBy(function($subscription) use ($period) {
                    $firstCompletePayment = $subscription->payments->where('status', 'Completed')->sortBy('paided_at')->first();
                    if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                        return (int) Carbon::parse($firstCompletePayment->paided_at)->endOfMonth()->startOfDay()->valueOf();
                    } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                        return (int) Carbon::parse($firstCompletePayment->paided_at)->endOfWeek()->startOfDay()->valueOf();
                    }
                });
            })->toArray();

        foreach($products as $productId => $subscriptions) {
            foreach ($subscriptions as $key => $value) {
                StatisticsModel::updateOrCreate([
                    'graph_id' => $graph->id,
                    'period_type' => $period,
                    // 'type' => StatisticsModel::SECOND_STATISTICS,
                    'product_id' => $productId,
                    'key' => $key,
                ], [
                    'value' => count($value ?? []),
                ]);
            }
        }
    }

    private function updateThirdStatistics(Graph $graph, array $productIds)
    {
        $period = $this->period;

        $products = Subscription::whereIn('product_id', $productIds)
            ->whereHas('payments', function ($q) {
                $q->where('status', 'Completed');
            })
            ->whereStatus('refused')
            ->get()
            ->groupBy('product_id')
            ->transform(function($items, $k) use ($period) {
                return $items->groupBy(function($subscription) use ($period) {
                    if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                        return (int) Carbon::parse($subscription->updated_at)->endOfMonth()->startOfDay()->valueOf();
                    } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                        return (int) Carbon::parse($subscription->updated_at)->endOfWeek()->startOfDay()->valueOf();
                    }
                });
            })->toArray();
        
        foreach($products as $productId => $subscriptions) {
            foreach ($subscriptions as $key => $value) {
                StatisticsModel::updateOrCreate([
                    'period_type' => $period,
                    'graph_id' => $graph->id,
                    // 'type' => StatisticsModel::THIRD_STATISTICS,
                    'product_id' => $productId,
                    'key' => $key,
                ], [
                    'value' => count($value ?? []),
                ]);
            }
        }
    }

    private function updateFourthStatistics(Graph $graph, array $productIds)
    {
        $period = $this->period;
        $products = Subscription::
            whereIn('product_id', $productIds)
            ->whereDoesntHave('payments', function($q) {
                $q->where('status', 'Completed');
            })
            ->whereStatus('refused')
            ->get()
            ->groupBy('product_id')
            ->transform(function($items, $k) use ($period) {
                return $items->groupBy(function($subscription) use ($period) {
                    if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                        return (int) Carbon::parse($subscription->updated_at)->endOfMonth()->startOfDay()->valueOf();
                    } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                        return (int) Carbon::parse($subscription->updated_at)->endOfWeek()->startOfDay()->valueOf();
                    }
                });
            })->toArray();
        
        foreach($products as $productId => $subscriptions) {
            foreach ($subscriptions as $key => $value) {
                StatisticsModel::updateOrCreate([
                    'period_type' => $period,
                    'product_id' => $productId,
                    'graph_id' => StatisticsModel::FOURTH_STATISTICS,
                    // 'type' => StatisticsModel::FOURTH_STATISTICS,
                    'key' => $key,
                ], [
                    'value' => count($value ?? []),
                ]);
            }
        }
    }

    private function updateSeventhStatistics(Graph $graph, array $productIds)
    {
        $period = $this->period;
        $products = Subscription::whereIn('product_id', $productIds)
            ->whereDoesntHave('payments', function($q) {
                $q->where('status', 'Completed');
            })
            ->whereStatus('refused')
            ->get()
            ->groupBy('product_id')
            ->transform(function($items, $k) use ($period) {
                return $items->groupBy(function($subscription) use ($period) {
                    if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                        return (int) Carbon::parse($subscription->started_at)->endOfMonth()->startOfDay()->valueOf();
                    } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                        return (int) Carbon::parse($subscription->started_at)->endOfWeek()->startOfDay()->valueOf();
                    }
                });
            })->toArray();
        
        foreach($products as $productId => $subscriptions) {
            foreach ($subscriptions as $key => $value) {
                StatisticsModel::updateOrCreate([
                    'period_type' => $period,
                    'product_id' => $productId,
                    'graph_id' => $graph->id,
                    // 'type' => StatisticsModel::SEVENTH_STATISTICS,
                    'key' => $key,
                ], [
                    'value' => count($value ?? []),
                ]);
            }
        }
    }

    private function updateEighthStatistics(Graph $graph, array $productIds)
    {
        $period = $this->period;
        $products = Subscription::whereIn('product_id', $productIds)
            ->whereHas('payments', function ($q) {
                $q->where('status', 'Completed');
            })
            ->whereStatus('refused')
            ->get()
            ->groupBy('product_id')
            ->transform(function($items, $k) use ($period) {
                return $items->groupBy(function($subscription) use ($period) {
                    if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                        return (int) Carbon::parse($subscription->started_at)->endOfMonth()->startOfDay()->valueOf();
                    } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                        return (int) Carbon::parse($subscription->started_at)->endOfWeek()->startOfDay()->valueOf();
                    }
                });
            })
            ->toArray();
        
        foreach($products as $productId => $subscriptions) {
            foreach ($subscriptions as $key => $value) {
                StatisticsModel::updateOrCreate([
                    'period_type' => $period,
                    // 'type' => StatisticsModel::EIGHTH_STATISTICS,
                    'graph_id' => $graph->id,
                    'product_id' => $productId,
                    'key' => $key,
                ], [
                    'value' => count($value ?? []),
                ]);
            }
        }
    }

    private function updateNinthStatistics(Graph $graph, array $productIds)
    {
        $period = $this->period;
        $products = Subscription::whereIn('product_id', $productIds)
            ->whereStatus('paid')
            ->whereHas('payments', function ($q) {
                $q->where('status', 'Completed');
            })->get()
            ->groupBy('product_id')
            ->transform(function($items, $k) use ($period) {
                return $items->groupBy(function($subscription) use ($period) {
                    if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                        return (int) Carbon::parse($subscription->started_at)->endOfMonth()->startOfDay()->valueOf();
                    } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                        return (int) Carbon::parse($subscription->started_at)->endOfWeek()->startOfDay()->valueOf();
                    }
                });
            })
            ->toArray();

        foreach($products as $productId => $subscriptions) {
            foreach ($subscriptions as $key => $value) {
                StatisticsModel::updateOrCreate([
                    'period_type' => $period,
                    'graph_id' => $graph->id,
                        // 'type' => StatisticsModel::NINTH_STATISTICS,
                    'product_id' => $productId,
                    'key' => $key,
                ], [
                    'value' => count($value ?? []),
                ]);
            }
        }
    }

    private function updateTenthStatistics(Graph $graph, array $productIds)
    {
        $period = $this->period;
        $products = Subscription::whereIn('product_id', $productIds)
            ->whereHas('payments', function (Builder $query) {
                $query->where('status', 'Completed');
            }, '=', 2)
            // ->whereStatus('refused')
            ->get()
            ->groupBy('product_id')
            ->transform(function($items, $k) use ($period) {
                return $items->groupBy(function($subscription) use ($period) {
                    $lastPayment = $subscription->payments()->latest()->first();
                    if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                        return (int) Carbon::parse($lastPayment->paided_at)->endOfMonth()->startOfDay()->valueOf();
                    } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                        return (int) Carbon::parse($lastPayment->paided_at)->endOfWeek()->startOfDay()->valueOf();
                    }
                });
            })
            ->toArray();
        
        foreach($products as $productId => $subscriptions) {
            foreach ($subscriptions as $key => $value) {
                StatisticsModel::updateOrCreate([
                    'period_type' => $period,
                    // 'type' => StatisticsModel::TENTH_STATISTICS,
                    'graph_id' => $graph->id,
                    'product_id' => $productId,
                    'key' => $key,
                ], [
                    'value' => count($value ?? []),
                ]);
            }
        }
    }

    private function updateEleventhStatistics(Graph $graph, array $productIds)
    {
        $period = $this->period;
        $products = Subscription::whereIn('product_id', $productIds)
            ->whereStatus('refused')
            ->whereHas('payments', function (Builder $query) {
                $query->where('status', 'Completed');
            }, '=', 1)
            ->get()
            ->groupBy('product_id')
            ->transform(function($items, $k) use ($period) {
                return $items->groupBy(function($subscription) use ($period) {
                    if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                        return (int) Carbon::parse($subscription->updated_at)->endOfMonth()->startOfDay()->valueOf();
                    } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                        return (int) Carbon::parse($subscription->updated_at)->endOfWeek()->startOfDay()->valueOf();
                    }
                });
            })
            ->toArray();

        foreach($products as $productId => $subscriptions) {
            foreach ($subscriptions as $key => $value) {
                StatisticsModel::updateOrCreate([
                    'period_type' => $period,
                    // 'type' => StatisticsModel::ELEVENTH_STATISTICS,
                    'graph_id' => $graph->id,
                    'product_id' => $productId,
                    'key' => $key,
                ], [
                    'value' => count($value ?? []),
                ]);
            }
        }
    }

    private function updateTwelfthStatistics(Graph $graph, array $productIds)
    {
        $period = $this->period;
        if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
            $date = (int) Carbon::now()->endOfMonth()->startOfDay()->valueOf();
        } else {
            $date = (int) Carbon::now()->endOfWeek()->startOfDay()->valueOf();
        }
        $products = Subscription::whereIn('product_id', $productIds)
            ->where('payment_type', 'cloudpayments')
            ->whereIn('status', ['paid', 'waiting'])
            ->whereIn('payment_type', ['transfer', 'cloudpayments'])
            ->whereHas('payments', function ($q) {
                $q->where('status', 'Completed');
            })
            ->get()
            ->groupBy('product_id')
            ->transform(function($items, $k) {
                return $items->count();
            })
            ->toArray();

        foreach($products as $productId => $subscriptionsCount) {
            StatisticsModel::updateOrCreate([
                'period_type' => $period,
                // 'type' => StatisticsModel::TWELFTH_STATISTICS,
                'graph_id' => $graph->id,
                'product_id' => $productId,
                'key' => $date,
            ], [
                'value' => $subscriptionsCount,
            ]);
        }
    }

    private function updateThirteenthStatistics(Graph $graph, array $productIds)
    {
        $period = $this->period;
        $products = Subscription::whereIn('product_id', $productIds)
            ->get()
            ->groupBy('product_id')
            ->transform(function($items, $k) use ($period) {
                return $items->groupBy(function($subscription) use ($period) {
                    if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                        return (int) Carbon::parse($subscription->started_at)->endOfMonth()->startOfDay()->valueOf();
                    } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                        return (int) Carbon::parse($subscription->started_at)->endOfWeek()->startOfDay()->valueOf();
                    }
                });
            })
            ->toArray();

        foreach($products as $productId => $subscriptions) {
            foreach ($subscriptions as $key => $value) {
                StatisticsModel::updateOrCreate([
                    'period_type' => $period,
                    'product_id' => $productId,
                    // 'type' => StatisticsModel::THIRTEENTH_STATISTICS,
                    'graph_id' => $graph->id,
                    'key' => $key,
                ], [
                    'value' => count($value ?? []),
                ]);
            }
        }
    }

    private function updateFifteenthStatistics(Graph $graph, array $productIds)
    {
        $period = $this->period;
        if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
            $date = (int) Carbon::now()->endOfMonth()->startOfDay()->valueOf();
        } else {
            $date = (int) Carbon::now()->endOfWeek()->startOfDay()->valueOf();
        }
        $products = Subscription::whereIn('product_id', $productIds)
            ->where('payment_type', 'transfer')
            ->whereIn('status', ['paid', 'waiting'])
            ->whereIn('payment_type', ['transfer', 'cloudpayments'])
            ->whereHas('payments', function ($q) {
                $q->where('status', 'Completed');
            })
            ->get()
            ->groupBy('product_id')
            ->transform(function($items, $k) {
                return $items->count();
            })
            ->toArray();

        foreach($products as $productId => $subscriptionsCount) {
            StatisticsModel::updateOrCreate([
                'period_type' => $period,
                // 'type' => StatisticsModel::FIFTEENTH_STATISTICS,
                'graph_id' => $graph->id,
                'product_id' => $productId,
                'key' => $date,
            ], [
                'value' => $subscriptionsCount,
            ]);
        }
    }

    private function updateSixteenthStatistics(Graph $graph, array $productIds)
    {
        $period = $this->period;
        if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
            $date = (int) Carbon::now()->endOfMonth()->startOfDay()->valueOf();
        } else {
            $date = (int) Carbon::now()->endOfWeek()->startOfDay()->valueOf();
        }

        $products = Subscription::whereIn('product_id', $productIds)
            ->whereIn('status', ['paid', 'waiting'])
            ->whereIn('payment_type', ['transfer', 'cloudpayments'])
            ->whereHas('payments', function ($q) {
                $q->where('status', 'Completed');
            })
            ->get()
            ->groupBy('product_id')
            ->transform(function($items, $k) {
                return $items->count();
            })
            ->toArray();

        foreach($products as $productId => $subscriptionsCount) {
            StatisticsModel::updateOrCreate([
                'period_type' => $period,
                // 'type' => StatisticsModel::SIXTEENTH_STATISTICS,
                'graph_id' => $graph->id,
                'product_id' => $productId,
                'key' => $date,
            ], [
                'value' => $subscriptionsCount,
            ]);
        }
    }

    private function updateSeventeenthStatistics(Graph $graph, array $productIds)
    {
        $period = $this->period;
        if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
            $date = (int) Carbon::now()->endOfMonth()->startOfDay()->valueOf();
        } else {
            $date = (int) Carbon::now()->endOfWeek()->startOfDay()->valueOf();
        }
        $products = \DB::table('payments')
            ->select('payments.*' ,\DB::raw('quantity * amount as total'))
            ->whereIn('product_id', $productIds)
            ->whereStatus('Completed')
            ->whereNull('deleted_at')
            ->get()
            ->groupBy('product_id')
            ->transform(function($items, $k) use ($period) {
                return $items->groupBy(function($payment) use ($period) {
                    if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                        return (int) Carbon::parse($payment->paided_at)->setTimezone('Asia/Almaty')->endOfMonth()->startOfDay()->valueOf();
                    } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                        return (int) Carbon::parse($payment->paided_at)->setTimezone('Asia/Almaty')->endOfWeek()->startOfDay()->valueOf();
                    }
                });
            })->toArray();

        foreach($products as $productId => $turnover) {
            dd($turnover);
            StatisticsModel::updateOrCreate([
                'period_type' => $period,
                // 'type' => StatisticsModel::SIXTEENTH_STATISTICS,
                'graph_id' => $graph->id,
                'product_id' => $productId,
                'key' => $date,
            ], [
                'value' => $turnover,
            ]);
        }
    }
}
