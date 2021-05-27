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

        $data = [
            'week' => [
                // 'key' => (int) Carbon::now()->subWeeks(20)->endOfWeek()->startOfDay()->valueOf(),
                // 'start' => Carbon::now()->setTimezone('Asia/Almaty')->subWeeks(20)->startOfWeek()->startOfDay(),
                // 'end' => Carbon::now()->setTimezone('Asia/Almaty')->subWeeks(20)->endOfWeek()->endOfDay(),
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
                $productBonuses = $product->bonuses;
    
                // Операторы услуги
                $productUsers = $product->users;

                // Все успешные платежи услуги
                $successPayments = $product->payments->where('status', 'Completed');

                foreach ($productBonuses as $productBonus) {
                    foreach (UsersBonuses::DATE_TYPES as $dateType) {
                        foreach ($productUsers as $user) {
                            // Проверка на то, высчитывать бонусы со дня вступления в команду
                            // Если в середине недели устроился, то считать по ней
                            $userEmploymentAt = Carbon::parse($user->pivot->employment_at);
                            if ($dateType == 'week') {
                                // $unixDate = $endOfWeekStartOfDay;
                                $unixDate = $data[$dateType]['key'];
                                $startedAt = $userEmploymentAt > Carbon::now()->startOfWeek()->startOfDay() ? $userEmploymentAt : Carbon::now()->startOfWeek()->startOfDay();
                                $groupSuccessPaymentsByBonusesBetweenWeek = $successPayments->whereBetween('paided_at', [
                                    // $startedAt,
                                    // Carbon::now()->endOfWeek()->endOfDay(),
                                    $data[$dateType]['start'],
                                    $data[$dateType]['end'], 
                                ])->groupBy('bonus_id');
                                $bonusAmount = isset($groupSuccessPaymentsByBonusesBetweenWeek[$productBonus->id]) ? $groupSuccessPaymentsByBonusesBetweenWeek[$productBonus->id]->count() : 0;
                            } else {
                                // $unixDate = $endOfMonthStartOfDay;
                                $unixDate = $data[$dateType]['key'];
                                $startedAt = $userEmploymentAt > Carbon::now()->startOfMonth()->startOfDay() ? $userEmploymentAt : Carbon::now()->startOfMonth()->startOfDay();
                                $groupSuccessPaymentsByBonusesBetweenMonth = $successPayments->whereBetween('paided_at', [
                                    // $startedAt,
                                    // Carbon::now()->endOfMonth()->endOfDay(),
                                    $data[$dateType]['start'],
                                    $data[$dateType]['end'],
                                ])->groupBy('bonus_id');
                                $bonusAmount = isset($groupSuccessPaymentsByBonusesBetweenMonth[$productBonus->id]) ? $groupSuccessPaymentsByBonusesBetweenMonth[$productBonus->id]->count() : 0;
                            }

                            if (isset($groupSuccessPaymentsByBonusesBetweenWeek[$productBonus->id])) {
                                $product->usersBonuses()->updateOrCreate([
                                    'bonus_id' => $productBonus->id,
                                    'date_type' => $dateType,
                                    'unix_date' => $unixDate,
                                    'user_id' => $user->id,
                                    'stake' => $user->pivot->stake,
                                ], [
                                    'amount' => $bonusAmount ?? 0,
                                ]);
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Log::error($e);
        }
    }
}
