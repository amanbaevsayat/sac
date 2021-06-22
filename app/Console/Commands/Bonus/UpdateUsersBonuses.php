<?php

namespace App\Console\Commands\Bonus;

use App\Models\Bonus;
use App\Models\Product;
use App\Models\Team;
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
        // $qwe = [
        //     1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25
        // ];
        // foreach ($qwe as $awd) {
            $data = [
                'week' => [
                    // 'key' => (int) Carbon::now()->subWeeks($awd)->endOfWeek()->startOfDay()->valueOf(),
                    // 'start' => Carbon::now()->setTimezone('Asia/Almaty')->subWeeks($awd)->startOfWeek()->startOfDay(),
                    // 'end' => Carbon::now()->setTimezone('Asia/Almaty')->subWeeks($awd)->endOfWeek()->endOfDay(),
                    'key' => (int) Carbon::now()->endOfWeek()->startOfDay()->valueOf(),
                    'start' => Carbon::now()->setTimezone('Asia/Almaty')->startOfWeek()->startOfDay(),
                    'end' => Carbon::now()->setTimezone('Asia/Almaty')->endOfWeek()->endOfDay(),
                ],
                'month' => [
                    // 'key' => (int) Carbon::now()->subMonths($awd)->endOfMonth()->startOfDay()->valueOf(),
                    // 'start' => Carbon::now()->setTimezone('Asia/Almaty')->subMonths($awd)->startOfMonth()->startOfDay(),
                    // 'end' => Carbon::now()->setTimezone('Asia/Almaty')->subMonths($awd)->endOfMonth()->endOfDay(),
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
                    // $productUsers = $product->users;
    
                    // Все успешные платежи услуги
                    $teamSuccessPayments = $product->payments->where('status', 'Completed')->where('team_id', '!=', null)->groupBy('team_id');
    
                    foreach ($teamSuccessPayments as $teamId => $successPayments) {
                        $team = Team::whereId($teamId)->first();
                        $teamUsers = $team->users;
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
                                    'team_id' => $teamId,
                                ], [
                                    'amount' => $bonusAmount ?? 0,
                                ]);

                                foreach ($teamUsers as $user) {
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
                                            'team_id' => $teamId,
                                        ],
                                    ]);
                                }
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                \Log::error($e);
            }
        // }
    }
}
