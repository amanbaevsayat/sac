<?php

namespace App\Console\Commands\Bonus;

use App\Models\Product;
use App\Models\UsersBonuses;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateUsersBonuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:users_bonuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда для обновления бонусов по продуктам';

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
        \Log::info('Обновление бонусов');
        $products = Product::all();
        $endOfWeekStartOfDay = (int) Carbon::now()->endOfWeek()->startOfDay()->valueOf();
        $endOfMonthStartOfDay = (int) Carbon::now()->endOfMonth()->startOfDay()->valueOf();

        foreach ($products as $product) {
            // Все бонусы продукта
            $productBonuses = $product->bonuses;

            // Операторы услуги
            $productUsers = $product->users->pluck('id')->toArray();
            $successPayments = $product->payments->where('status', 'Completed');
            $groupByBonuses = $successPayments->groupBy('bonus_id');
            foreach ($productBonuses as $productBonus) {
                if (isset($groupByBonuses[$productBonus->id])) {
                    foreach (UsersBonuses::DATE_TYPES as $dateType) {
                        if ($dateType == 'week') {
                            $unixDate = $endOfWeekStartOfDay;
                            $bonusAmount = $groupByBonuses[$productBonus->id]->whereBetween('paided_at', [
                                Carbon::now()->startOfWeek()->startOfDay(),
                                Carbon::now()->endOfWeek()->endOfDay(),
                            ])->count();
                        } else {
                            $unixDate = $endOfMonthStartOfDay;
                            $bonusAmount = $groupByBonuses[$productBonus->id]->whereBetween('paided_at', [
                                Carbon::now()->startOfMonth()->startOfDay(),
                                Carbon::now()->endOfMonth()->endOfDay(),
                            ])->count();
                        }
    
                        $product->usersBonuses()->updateOrCreate([
                            'bonus_id' => $productBonus->id,
                            'date_type' => $dateType,
                            'unix_date' => $unixDate,
                        ], [
                            'user_ids' => $productUsers,
                            'amount' => $bonusAmount, // TODO
                        ]);
                    }
                }
            }
        }
    }
}
