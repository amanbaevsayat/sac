<?php

namespace App\Console\Commands\Bonus;

use App\Models\Bonus;
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
        $products = Product::all();
        $endOfWeekStartOfDay = (int) Carbon::now()->endOfWeek()->startOfDay()->valueOf();
        $endOfMonthStartOfDay = (int) Carbon::now()->endOfMonth()->startOfDay()->valueOf();

        $data = [
            'week' => [
                // 'key' => (int) Carbon::now()->subWeeks(1)->endOfWeek()->startOfDay()->valueOf(),
                // 'start' => Carbon::now()->setTimezone('Asia/Almaty')->subWeeks(1)->startOfWeek()->startOfDay(),
                // 'end' => Carbon::now()->setTimezone('Asia/Almaty')->subWeeks(1)->endOfWeek()->endOfDay(),
                'key' => (int) Carbon::now()->endOfWeek()->startOfDay()->valueOf(),
                'start' => Carbon::now()->setTimezone('Asia/Almaty')->startOfWeek()->startOfDay(),
                'end' => Carbon::now()->setTimezone('Asia/Almaty')->endOfWeek()->endOfDay(),
            ],
            'month' => [
                // 'key' => (int) Carbon::now()->subMonths(1)->endOfMonth()->startOfDay()->valueOf(),
                // 'start' => Carbon::now()->setTimezone('Asia/Almaty')->subMonths(1)->startOfMonth()->startOfDay(),
                // 'end' => Carbon::now()->setTimezone('Asia/Almaty')->subMonths(1)->endOfMonth()->endOfDay(),
                'key' => (int) Carbon::now()->endOfMonth()->startOfDay()->valueOf(),
                'start' => Carbon::now()->setTimezone('Asia/Almaty')->startOfMonth()->startOfDay(),
                'end' => Carbon::now()->setTimezone('Asia/Almaty')->endOfMonth()->endOfDay(),
            ],
        ];

        try {
            foreach ($products as $product) {
                // Все бонусы продукта
                // Пример. Первый платеж по подписке
                $productBonuses = $product->productBonuses;
    
                // Операторы услуги
                $productUsers = $product->users;

                // Все успешные платежи услуги
                $successPayments = $product->payments->where('status', 'Completed');

                foreach ($productBonuses as $productBonus) {
                    foreach (Bonus::DATE_TYPES as $dateType) {
                        if ($dateType == 'week') {
                            $unixDate = $data[$dateType]['key'];
                            $groupSuccessPaymentsByBonusesBetweenWeek = $successPayments->whereBetween('paided_at', [
                                $data[$dateType]['start'],
                                $data[$dateType]['end'], 
                            ])->groupBy('product_bonus_id');
                            $bonusAmount = isset($groupSuccessPaymentsByBonusesBetweenWeek[$productBonus->id]) ? $groupSuccessPaymentsByBonusesBetweenWeek[$productBonus->id]->count() : 0;
                        } else {
                            $unixDate = $data[$dateType]['key'];
                            $groupSuccessPaymentsByBonusesBetweenMonth = $successPayments->whereBetween('paided_at', [
                                $data[$dateType]['start'],
                                $data[$dateType]['end'],
                            ])->groupBy('product_bonus_id');
                            $bonusAmount = isset($groupSuccessPaymentsByBonusesBetweenMonth[$productBonus->id]) ? $groupSuccessPaymentsByBonusesBetweenMonth[$productBonus->id]->count() : 0;
                        }

                        $bonus = $product->bonuses()->updateOrCreate([
                            'product_bonus_id' => $productBonus->id,
                            'date_type' => $dateType,
                            'unix_date' => $unixDate,
                        ], [
                            'amount' => $bonusAmount ?? 0,
                        ]);

                        foreach ($productUsers as $user) {
                            // Проверка на то, высчитывать бонусы со дня вступления в команду
                            // Если в середине недели устроился, то считать по ней
                            $userEmploymentAt = Carbon::parse($user->pivot->employment_at);

                            if ($dateType == 'week') {
                                $unixDate = $data[$dateType]['key'];
                                $startedAt = $userEmploymentAt > $data[$dateType]['start'] ? $userEmploymentAt : $data[$dateType]['start'];
                                $groupSuccessPaymentsByBonusesBetweenWeek = $successPayments->whereBetween('paided_at', [
                                    $startedAt,
                                    $data[$dateType]['end'], 
                                ])->groupBy('product_bonus_id');
                                $bonusAmount = isset($groupSuccessPaymentsByBonusesBetweenWeek[$productBonus->id]) ? $groupSuccessPaymentsByBonusesBetweenWeek[$productBonus->id]->count() : 0;
                            } else {
                                $unixDate = $data[$dateType]['key'];
                                $startedAt = $userEmploymentAt > $data[$dateType]['start'] ? $userEmploymentAt : $data[$dateType]['start'];
                                $groupSuccessPaymentsByBonusesBetweenMonth = $successPayments->whereBetween('paided_at', [
                                    $startedAt,
                                    $data[$dateType]['end'],
                                ])->groupBy('product_bonus_id');
                                $bonusAmount = isset($groupSuccessPaymentsByBonusesBetweenMonth[$productBonus->id]) ? $groupSuccessPaymentsByBonusesBetweenMonth[$productBonus->id]->count() : 0;
                            }
                            $user->bonuses()->detach([$bonus->id]);

                            $user->bonuses()->attach([
                                $bonus->id => [
                                    'stake' => $user->pivot->stake,
                                    'bonus_amount' => $bonusAmount,
                                ],
                            ]);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::error($e);
        }
    }
}
