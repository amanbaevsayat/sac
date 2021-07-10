<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductWithUsersResource;
use App\Models\Bonus;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StatisticsModel;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\User;
use App\Models\UsersBonuses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UsersBonusesController extends Controller
{
    private function getPeriods($periodType, $from, $to)
    {
        $start = new \DateTime($from);
        $end   = new \DateTime($to);
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($start, $interval, $end);
        $data = [];
        foreach ($period as $dt)
        {
            if ($periodType == 'week') {
                $day = (int) Carbon::parse($dt)->endOfWeek()->startOfDay()->valueOf();
                if (! in_array($day, $data)) {
                    $data[] = $day;
                }
            } else if ($periodType == 'month') {
                $day = (int) Carbon::parse($dt)->endOfMonth()->startOfDay()->valueOf();
                if (! in_array($day, $data)) {
                    $data[] = $day;
                }
            }
        }
        return $data;
    }

    private function getCurrentAndLastPeriod($periodType)
    {
        if ($periodType == 'week') {
            $current = (int) Carbon::now()->endOfWeek()->startOfDay()->valueOf();
            $last = (int) Carbon::now()->subWeek()->endOfWeek()->startOfDay()->valueOf();
        } else if ($periodType == 'month') {
            $current = (int) Carbon::now()->endOfMonth()->startOfDay()->valueOf();
            $last = (int) Carbon::now()->subMonth()->endOfMonth()->startOfDay()->valueOf();
        }

        return [
            'current' => $current ?? null,
            'last' => $last ?? null,
        ];
    }

    private function getTeamId(User $user, $teamId)
    {
        if ($user->isOperator()) {
            if (! $teamId) {
                $team = Team::whereHas('users', function ($query) use ($user) {
                    $query->where('id', $user->id);
                })->first();

                if (isset($team)) {
                    return $team->id;
                } else {
                    abort(404, 'У оператора нет присвоенных услуг');
                }
            } else {
                $team = Team::whereId($teamId)->whereHas('users', function ($query) use ($user) {
                    $query->where('id', $user->id);
                })->first();

                if (isset($team)) {
                    return $team->id;
                } else {
                    $team = Team::whereHas('users', function ($query) use ($user) {
                        $query->where('id', $user->id);
                    })->first();

                    if (isset($team)) {
                        return $team->id;
                    } else {
                        abort(404, 'У оператора нет присвоенных услуг');
                    }
                }
            }
        } else {
            if (! $teamId) {
                return Team::first()->id;
            } else {
                $team = Team::whereId($teamId)->first();

                if (isset($team)) {
                    return $teamId;
                } else {
                    $team = Team::first();

                    if (isset($team)) {
                        return $team->id;
                    } else {
                        abort(404, 'Нет активных услуг');
                    }
                }
            }
        }
    }

    public function show(Request $request)
    {
        access(['can-head', 'can-host', 'can-operator']);
        $user = User::whereId(Auth::id())->firstOrFail();
        $teams = $user->isOperator() ? Team::select('id', 'name')->whereHas('users', function ($query) use ($user) {
            $query->where('id', $user->id);
        })->get()->toArray() : Team::select('id', 'name')->get()->toArray();
        $getCurrentAndLastPeriod = $this->getCurrentAndLastPeriod($request->input('period', 'week'));
        $data = [];
        $data['teamId'] = $this->getTeamId($user, $request->get('teamId'));
        $products = Product::get()->pluck('title', 'id')->toArray();

        if (
            ! $request->has('currentPoint') ||
            ! $request->has('lastPoint') ||
            ! $request->has('period') ||
            ! $request->has('from') || 
            ! $request->has('to') ||
            $data['teamId'] != $request->get('teamId')
        ) {
            $data['currentPoint']   = $request->input('point') ?? $getCurrentAndLastPeriod['current'];
            $data['lastPoint']      = $request->input('point') ?? $getCurrentAndLastPeriod['last'];
            $data['period']         = $request->input('period') ?? 'week';
            $data['from']           = $request->input('from') ?? Carbon::now()->subMonths(3)->format('Y-m-d');
            $data['to']             = $request->input('to') ?? Carbon::now()->format('Y-m-d');

            return redirect()->route('users_bonuses.show', $data);
        }

        $request->validate([
            "from"      => "required|date_format:Y-m-d",
            "to"        => "required|date_format:Y-m-d",
            "teamId"    => "required",
            "period"    => "required",
        ]);

        $period     = $request->input('period');
        $teamId     = $request->input('teamId');
        $from       = Carbon::createFromFormat('Y-m-d', $request->input('from'), 'Asia/Almaty')->startOfDay()->setTimezone('Asia/Almaty');
        $to         = Carbon::createFromFormat('Y-m-d', $request->input('to'), 'Asia/Almaty')->endOfDay()->setTimezone('Asia/Almaty');
        $categories = $this->getPeriods($period, $from, $to);

        $usersBonuses = Bonus::join('product_bonuses', 'product_bonuses.id', '=', 'product_bonus_id')
            ->join('payment_types', 'payment_types.id', '=', 'product_bonuses.payment_type_id')
            ->select(
                'bonuses.*',
                'product_bonuses.type',
                'product_bonuses.amount as product_bonuses_amount',
                'payment_types.name as payment_type',
                \DB::raw('(bonuses.amount * product_bonuses.amount) as total_bonus')
            )
            ->where('bonuses.team_id', $teamId)
            ->where('bonuses.date_type', $period)
            ->where('product_bonuses.amount', '>', 0)
            ->orderBy('payment_types.name')
            ->get()
            // ->where('total_bonus', '>', 0)
            ->groupBy('unix_date')
            ->transform(function($item, $k) {
                return $item->groupBy(function ($item, $key) {
                    return $item->product_id;
                })->transform(function ($item, $key) {
                    return $item->groupBy(function ($item, $key) {
                        return $item->payment_type . '-' . $item->type;
                    })->sortKeys();
                });
            })
            ->toArray();

        $recordsBonuses = Bonus::join('product_bonuses', 'product_bonuses.id', '=', 'product_bonus_id')
            ->join('payment_types', 'payment_types.id', '=', 'product_bonuses.payment_type_id')
            ->select(
                \DB::raw('MAX(bonuses.amount) AS max_amount'),
                \DB::raw("CONCAT(payment_types.name,'-',product_bonuses.type) as bonusType")
            )
            ->where('bonuses.team_id', $teamId)
            ->where('bonuses.date_type', $period)
            ->groupBy(['bonusType'])
            ->get()
            ->pluck('max_amount', 'bonusType')
            ->toArray();

        $usersBonusesForChart = Bonus::join('product_bonuses', 'product_bonuses.id', '=', 'product_bonus_id')
            ->join('payment_types', 'payment_types.id', '=', 'product_bonuses.payment_type_id')
            ->select(
                \DB::raw("SUM(product_bonuses.amount * bonuses.amount) as total_bonus"),
                'bonuses.unix_date'
            )
            ->where('bonuses.team_id', $teamId)
            ->where('bonuses.date_type', $period)
            ->groupBy('bonuses.unix_date')
            ->get()
            ->pluck('total_bonus', 'unix_date')
            ->toArray();

        $usersBonusesGroupByUnixDate = Bonus::join('bonus_user', 'bonus_user.bonus_id', '=', 'bonuses.id')
            ->join('product_bonuses', 'product_bonuses.id', '=', 'bonuses.product_bonus_id')
            ->join('users', 'users.id', '=', 'bonus_user.user_id')
            ->select(
                'bonuses.unix_date',
                'users.name',
                'bonus_user.stake',
                \DB::raw("SUM(bonus_user.bonus_amount * product_bonuses.amount * bonus_user.stake / 100) as total_bonus"),
            )
            ->where('bonuses.team_id', $teamId)
            ->where('bonuses.date_type', $period)
            ->groupBy('bonuses.unix_date', 'bonus_user.user_id', 'bonus_user.stake')
            ->get()
            ->transform(function ($item) {
                return [
                    'unix_date' => $item['unix_date'],
                    'name' => $item['name'],
                    'stake' => $item['stake'],
                    'total_bonus' => number_format($item['total_bonus'], 0, '.', ' '),
                ];
            })
            ->groupBy('unix_date')
            ->toArray();

        $chart = [
            'type' => 'highchart',
            'chart' => [
                'type' => 'area',
            ],
            "title" => ["text" => 'Сумма бонусов команды за '. ($period == 'week' ? 'неделю' : 'месяц')],
            'xAxis' => [
                'type' => 'datetime',
            ],
            "series" => [
                [
                    'editable' => false,
                    "name" => "Сумма",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($usersBonusesForChart) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($usersBonusesForChart[$category] ?? 0)];
                    })->toArray()),
                    "color" => "#c2de80",
                ],
            ],
            'plotOptions' => [
                'area' => [
                    'fillOpacity' => 0.5,
                    'dataLabels' => [
                        'enabled' => true,
                    ],
                ],
            ],
        ];

        return view('users-bonuses.show', compact(
            'teams',
            'products',
            'chart',
            'usersBonuses',
            'usersBonusesForChart',
            'recordsBonuses',
            'usersBonusesGroupByUnixDate'
        ));
    }
}
