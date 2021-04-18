<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Product;
use App\Models\StatisticsModel;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class StatisticsController extends Controller
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
            if ($periodType == StatisticsModel::PERIOD_TYPE_WEEK) {
                $day = (int) Carbon::parse($dt)->endOfWeek()->startOfDay()->valueOf();
                if (! in_array($day, $data)) {
                    $data[] = $day;
                }
            } else if ($periodType == StatisticsModel::PERIOD_TYPE_MONTH) {
                $day = (int) Carbon::parse($dt)->endOfMonth()->startOfDay()->valueOf();
                if (! in_array($day, $data)) {
                    $data[] = $day;
                }
            }
        }
        return $data;
    }

    public function quantitative(Request $request)
    {
        access(['can-head', 'can-host']);
        if (!$request->has('from') && !$request->has('to')) {
            return redirect()->route('statistics.quantitative', [
                "period" => $request->input('period') ?? StatisticsModel::PERIOD_TYPE_WEEK,
                "productId" => $request->input('productId') ?? Product::first()->id ?? null,
                "from" => $request->input('from') ?? Carbon::now()->subMonths(3)->format('Y-m-d'),
                "to" => $request->input('to') ?? Carbon::now()->format('Y-m-d'),
            ]);
        }

        $products = Product::get()->pluck('title', 'id');

        $request->validate([
            "from" => "required|date_format:Y-m-d",
            "to" => "required|date_format:Y-m-d",
            "productId" => "required",
            "period" => "required",
        ]);
        
        $chats = collect();
        $from = Carbon::createFromFormat('Y-m-d', $request->input('from'), 'Asia/Almaty')->startOfDay()->setTimezone('Asia/Almaty');
        $to = Carbon::createFromFormat('Y-m-d', $request->input('to'), 'Asia/Almaty')->endOfDay()->setTimezone('Asia/Almaty');
        $categories = $this->getPeriods($request->get('period'), $from, $to);
        $productId = $request->input('productId');

        $newLeadsFirst = StatisticsModel::where('period_type', $request->get('period'))->where('product_id', $request->get('productId'))->where('type', StatisticsModel::FIRST_STATISTICS)->get()->pluck('value', 'key');
        $newLeadsSecond = StatisticsModel::where('period_type', $request->get('period'))->where('product_id', $request->get('productId'))->where('type', StatisticsModel::SECOND_STATISTICS)->get()->pluck('value', 'key');
        $chats->push([
            'chart' => [
                'type' => 'area',
            ],
            "title" => ["text" => 'Новые лиды'],
            "xAxis" => [
                'type' => 'datetime',
            ],
            "series" => [
                [
                    'editable' => false,
                    'statisticsType' => StatisticsModel::SECOND_STATISTICS,
                    "name" => "Новые клиенты",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($newLeadsSecond) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($newLeadsSecond[$category] ?? 0)];
                    })->toArray()),
                    "color" => "#c2de80",
                    'description' => 'Оплатили первый платеж в этот период.',
                ],
                [
                    'editable' => true,
                    'statisticsType' => StatisticsModel::FIRST_STATISTICS,
                    "name" => "Новые лиды (Instagram)",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($newLeadsFirst) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($newLeadsFirst[$category] ?? 0)];
                    })->toArray()),
                    "color" => "#db9876",
                    'description' => 'Данные загружаются вручную',
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
        ]);

        $outflowClients = StatisticsModel::where('period_type', $request->get('period'))->where('product_id', $request->get('productId'))->where('type', StatisticsModel::THIRD_STATISTICS)->get()->pluck('value', 'key');
        $outflowTrials = StatisticsModel::where('period_type', $request->get('period'))->where('product_id', $request->get('productId'))->where('type', StatisticsModel::FOURTH_STATISTICS)->get()->pluck('value', 'key');

        $chats->push([
            'chart' => [
                'type' => 'area',
            ],
            "title" => ["text" => 'Динамика базы клиентов (по факту)'],
            'xAxis' => [
                'type' => 'datetime',
            ],
            "series" => [
                [
                    'editable' => false,
                    "name" => "Приток клиентов",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($newLeadsSecond) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($newLeadsSecond[$category] ?? 0)];
                    })->toArray()),
                    "color" => "#c2de80",
                    'description' => 'Оплатили первый платеж в этот период.',
                ],
                [
                    'editable' => false,
                    "name" => "Отток клиентов",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($outflowClients) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($outflowClients[$category] ?? 0)];
                    })->toArray()),
                    "color" => "#db9876",
                    'description' => 'Есть хотя бы один платеж | Отказались в этот период',
                ],
                [
                    'visible' => false,
                    'editable' => false,
                    "name" => "Отток пробных",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($outflowTrials) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($outflowTrials[$category] ?? 0)];
                    })->toArray()),
                    "color" => "#2adaca",
                    'description' => 'Нету платежа | Отказались в этот период',
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
        ]);

        $inflowClientsAtStartedAt = StatisticsModel::where('period_type', $request->get('period'))->where('product_id', $request->get('productId'))->where('type', StatisticsModel::NINTH_STATISTICS)->get()->pluck('value', 'key');
        $outflowClientsAtStartedAt = StatisticsModel::where('period_type', $request->get('period'))->where('product_id', $request->get('productId'))->where('type', StatisticsModel::EIGHTH_STATISTICS)->get()->pluck('value', 'key');
        $outflowTrialsAtStartedAt = StatisticsModel::where('period_type', $request->get('period'))->where('product_id', $request->get('productId'))->where('type', StatisticsModel::SEVENTH_STATISTICS)->get()->pluck('value', 'key');
        $chats->push([
            'chart' => [
                'type' => 'area',
            ],
            "title" => ["text" => 'Динамика базы клиентов (по дате старта)'],
            'xAxis' => [
                'type' => 'datetime',
            ],
            "series" => [
                [
                    'editable' => false,
                    "name" => "Приток клиентов",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($inflowClientsAtStartedAt) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($inflowClientsAtStartedAt[$category] ?? 0)];
                    })->toArray()),
                    "color" => "#c2de80",
                    'description' => 'Клиенты которые подключились в этот период, занимаются на данный момент',
                ],
                [
                    'editable' => false,
                    "name" => "Отток клиентов",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($outflowClientsAtStartedAt) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($outflowClientsAtStartedAt[$category] ?? 0)];
                    })->toArray()),
                    "color" => "#db9876",
                    'description' => 'Есть хотя бы один платеж | Клиенты которые подключились в этот период, отказались на данный момент',
                ],
                [
                    'visible' => false,
                    'editable' => false,
                    "name" => "Отток пробных",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($outflowTrialsAtStartedAt) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($outflowTrialsAtStartedAt[$category] ?? 0)];
                    })->toArray()),
                    'description' => 'Нету платежа | Пробные которые подключились в этот период, отказались на данный момент',
                    "color" => "#2adaca",
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
        ]);

        $twoPaymentsSubscriptions = StatisticsModel::where('period_type', $request->get('period'))->where('product_id', $request->get('productId'))->where('type', StatisticsModel::TENTH_STATISTICS)->get()->pluck('value', 'key');
        $onePaymentRefusedSubscriptions = StatisticsModel::where('period_type', $request->get('period'))->where('product_id', $request->get('productId'))->where('type', StatisticsModel::ELEVENTH_STATISTICS)->get()->pluck('value', 'key');

        $chats->push([
            'chart' => [
                'type' => 'area',
            ],
            "title" => ["text" => 'Оплата второго месяца'],
            'xAxis' => [
                'type' => 'datetime',
            ],
            "series" => [
                [
                    'editable' => false,
                    "name" => "Купили второй абонемент.",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($twoPaymentsSubscriptions) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($twoPaymentsSubscriptions[$category] ?? 0)];
                    })->toArray()),
                    "color" => "#c2de80",
                    'description' => '',
                ],
                [
                    'editable' => false,
                    "name" => "Есть один платеж, но отказались",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($onePaymentRefusedSubscriptions) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($onePaymentRefusedSubscriptions[$category] ?? 0)];
                    })->toArray()),
                    "color" => "#db9876",
                    'description' => '',
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
        ]);

        $activeSubscriptions = StatisticsModel::where('period_type', $request->get('period'))->where('product_id', $request->get('productId'))->where('type', StatisticsModel::TWELFTH_STATISTICS)->get()->pluck('value', 'key');
        $chats->push([
            'chart' => [
                'type' => 'area',
            ],
            "title" => ["text" => 'Активные абонементы.'],
            'xAxis' => [
                'type' => 'datetime',
            ],
            "series" => [
                [
                    'editable' => true,
                    "name" => "Активные абонементы.",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($activeSubscriptions) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($activeSubscriptions[$category] ?? 0)];
                    })->toArray()),
                    "color" => "#c2de80",
                    'statisticsType' => StatisticsModel::TWELFTH_STATISTICS,
                    'description' => 'Тип оплаты: (cloudpayments, перевод) | Есть один платеж | Статус абонемента: (Оплачено, Жду оплату)',
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
        ]);

        return view('pages.statistics', compact('products', 'chats'));
    }

    public function financial(Request $request)
    {
        access(['can-head', 'can-host']);
        if (!$request->has('from') && !$request->has('to')) {
            return redirect()->route('statistics.financial', [
                "period" => $request->input('period') ?? StatisticsModel::PERIOD_TYPE_WEEK,
                "productId" => $request->input('productId') ?? Product::first()->id ?? null,
                "from" => $request->input('from') ?? Carbon::now()->subMonths(3)->format('Y-m-d'),
                "to" => $request->input('to') ?? Carbon::now()->format('Y-m-d'),
            ]);
        }

        $products = Product::get()->pluck('title', 'id');

        $request->validate([
            "from" => "required|date_format:Y-m-d",
            "to" => "required|date_format:Y-m-d",
            "productId" => "required",
            "period" => "required",
        ]);

        $chats = collect();
        $period = $request->input('period');
        $from = Carbon::createFromFormat('Y-m-d', $request->input('from'), 'Asia/Almaty')->startOfDay()->setTimezone('Asia/Almaty');
        $to = Carbon::createFromFormat('Y-m-d', $request->input('to'), 'Asia/Almaty')->endOfDay()->setTimezone('Asia/Almaty');
        $categories = $this->getPeriods($request->get('period'), $from, $to);
        $productId = $request->input('productId');
        $turnover = \DB::table('payments')
            ->select('payments.*' ,\DB::raw('quantity * amount as total'))
            ->whereStatus('Completed')
            ->whereNull('deleted_at')
            ->whereBetween('paided_at', [$from, $to])
            ->get()
            ->groupBy(function($payment) use ($period) {
                if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                    return (int) Carbon::parse($payment->paided_at)->setTimezone('Asia/Almaty')->endOfMonth()->startOfDay()->valueOf();
                } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                    return (int) Carbon::parse($payment->paided_at)->setTimezone('Asia/Almaty')->endOfWeek()->startOfDay()->valueOf();
                }
            });
        
        $turnoverCloudpayments = \DB::table('payments')
            ->select('payments.*' ,\DB::raw('quantity * amount as total'))
            ->whereNull('deleted_at')
            ->whereType('cloudpayments')
            ->whereStatus('Completed')
            ->whereBetween('paided_at', [$from, $to])
            ->get()
            ->groupBy(function($payment) use ($period) {
                if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                    return (int) Carbon::parse($payment->paided_at)->endOfMonth()->startOfDay()->valueOf();
                } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                    return (int) Carbon::parse($payment->paided_at)->endOfWeek()->startOfDay()->valueOf();
                }
            });
        
        $turnoverTransfers = \DB::table('payments')
            ->select('payments.*' ,\DB::raw('quantity * amount as total'))
            ->whereNull('deleted_at')
            ->whereType('transfer')
            ->whereStatus('Completed')
            ->whereBetween('paided_at', [$from, $to])
            ->get()
            ->groupBy(function($payment) use ($period) {
                if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                    return (int) Carbon::parse($payment->paided_at)->endOfMonth()->startOfDay()->valueOf();
                } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                    return (int) Carbon::parse($payment->paided_at)->endOfWeek()->startOfDay()->valueOf();
                }
            });

        $chats->push([
            'chart' => [
                'type' => 'area',
            ],
            "title" => ["text" => 'Оборот'],
            "xAxis" => [
                "type" => 'datetime',
            ],
            "series" => [
                [
                    'editable' => false,
                    "name" => "Общий оборот",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($turnover) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => isset($turnover[$category]) ? $turnover[$category]->sum('total') : 0];
                    })->toArray()),
                    "color" => "#c2de80",
                    "description" => "Суммирование всех платежей (цена * количество)",
                ],
                [
                    'editable' => false,
                    "name" => "Оборот по переводам",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($turnoverTransfers) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => isset($turnoverTransfers[$category]) ? $turnoverTransfers[$category]->sum('total') : 0];
                    })->toArray()),
                    "color" => "#e8bf29",
                    "description" => "Прямой перевод | Суммирование всех платежей (цена * количество)",
                ],
                [
                    'editable' => false,
                    "name" => "Оборот по подпискам",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($turnoverCloudpayments) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => isset($turnoverCloudpayments[$category]) ? $turnoverCloudpayments[$category]->sum('total') : 0];
                    })->toArray()),
                    "color" => "#2adaca",
                    "description" => "Cloudpayments | Суммирование всех платежей (цена * количество)",
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
        ]);

        return view('pages.statistics', compact('products', 'chats'));
    }

    public function update(Request $request)
    {
        access(['can-operator', 'can-head', 'can-host']);

        $request->validate([
            "item.statisticsType" => "required",
            "item.data" => "required",
            "productId" => "required",
            "period" => "required",
        ]);
        foreach ($request->input('item.data') as $item) {
            StatisticsModel::updateOrCreate([
                'period_type' => $request->get('period'),
                'product_id' => (int) $request->get('productId'),
                'type' => (int) $request->input('item.statisticsType'),
                'key' => $item['x'],
            ], [
                'value' => $item['y'],
            ]);
        }

        return response()->json([
            'message' => 'Данные успешно сохранены.',
        ], 200);
    }
}
