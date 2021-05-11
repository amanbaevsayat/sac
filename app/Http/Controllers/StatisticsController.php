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
        $connectedToWhatsapp = StatisticsModel::where('period_type', $request->get('period'))->where('product_id', $request->get('productId'))->where('type', StatisticsModel::THIRTEENTH_STATISTICS)->get()->pluck('value', 'key');
        $chats->push([
            'type' => 'highchart',
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
                    "name" => "Подключились в WhatsApp",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($connectedToWhatsapp) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($connectedToWhatsapp[$category] ?? 0)];
                    })->toArray()),
                    "color" => "#c2de80",
                    'description' => 'Подключились к WhatsApp - Все у кого дата старта абонемента началась в этот период',
                ],
                [
                    'editable' => true,
                    'statisticsType' => StatisticsModel::FIRST_STATISTICS,
                    "name" => "Новые лиды (Instagram)",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($newLeadsFirst) {
                        return [
                            'name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 
                            'x' => $category, 
                            'y' => (int) ($newLeadsFirst[$category] ?? 0),
                        ];
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

        $chats->push([
            'type' => 'highchart',
            'chart' => [
                'type' => 'area',
            ],
            "title" => ["text" => 'Конверсия подключении к WhatsApp'],
            "xAxis" => [
                'type' => 'datetime',
            ],
            'yAxis' => [
                'labels' => [
                    'format' => '{value}%'
                ],
                'title' => [
                    'enabled' => false
                ],
            ],
            "series" => [
                [
                    // 'editable' => true,
                    'statisticsType' => StatisticsModel::FIRST_STATISTICS,
                    "name" => "Новые лиды (Instagram)",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($newLeadsFirst, $connectedToWhatsapp) {
                        $newLead = (is_numeric($newLeadsFirst[$category] ?? 0) && ($newLeadsFirst[$category] ?? 0) != 0) ? $newLeadsFirst[$category] : 1;
                        $connectToWhatsapp = (is_numeric($connectedToWhatsapp[$category] ?? 0) && ($connectedToWhatsapp[$category] ?? 0) != 0) ? $connectedToWhatsapp[$category] : 1;
                        // dd($newLead, $connectToWhatsapp);
                        $conversion = ($newLead > $connectToWhatsapp) ? round($connectToWhatsapp / $newLead * 100, 1) : 0;
                        // dd($conversion);
                        return [
                            'name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 
                            'x' => $category, 
                            'y' => $conversion,
                        ];
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
        $inflowClientsAtStartedAt = StatisticsModel::where('period_type', $request->get('period'))->where('product_id', $request->get('productId'))->where('type', StatisticsModel::NINTH_STATISTICS)->get()->pluck('value', 'key');

        $chats->push([
            'type' => 'highchart',
            'chart' => [
                'type' => 'area',
            ],
            "title" => ["text" => 'Конверсия из пробных в клиенты'],
            "xAxis" => [
                'type' => 'datetime',
            ],
            'yAxis' => [
                'labels' => [
                    'format' => '{value}%'
                ],
                'title' => [
                    'enabled' => false
                ],
            ],
            "series" => [
                [
                    // 'editable' => true,
                    'statisticsType' => StatisticsModel::FIRST_STATISTICS,
                    "name" => "Конверсия из пробных в клиенты",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($inflowClientsAtStartedAt, $connectedToWhatsapp) {
                        $conWhat = (is_numeric($connectedToWhatsapp[$category] ?? 0) && ($connectedToWhatsapp[$category] ?? 0) != 0) ? $connectedToWhatsapp[$category] : 1;
                        $clients = (is_numeric($inflowClientsAtStartedAt[$category] ?? 0) && ($inflowClientsAtStartedAt[$category] ?? 0) != 0) ? $inflowClientsAtStartedAt[$category] : 1;
                        $conversion = ($conWhat > $clients) ? round($clients / $conWhat * 100, 1) : 0;
                        // dd($conversion);
                        return [
                            'name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 
                            'x' => $category, 
                            'y' => $conversion,
                        ];
                    })->toArray()),
                    "color" => "#db9876",
                    'description' => '(По дате старта) Клиенты совершившие 1 платеж/ Подключились к WhatsApp * 100',
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
        $chats->push([
            'type' => 'highchart',
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

        $outflowClientsAtStartedAt = StatisticsModel::where('period_type', $request->get('period'))->where('product_id', $request->get('productId'))->where('type', StatisticsModel::EIGHTH_STATISTICS)->get()->pluck('value', 'key');
        $outflowTrialsAtStartedAt = StatisticsModel::where('period_type', $request->get('period'))->where('product_id', $request->get('productId'))->where('type', StatisticsModel::SEVENTH_STATISTICS)->get()->pluck('value', 'key');
        $chats->push([
            'type' => 'highchart',
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
            'type' => 'highchart',
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
                    'description' => 'Количество клиентов, у которых два успешных платежа',
                ],
                [
                    'editable' => false,
                    "name" => "Есть один платеж, но отказались",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($onePaymentRefusedSubscriptions) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($onePaymentRefusedSubscriptions[$category] ?? 0)];
                    })->toArray()),
                    "color" => "#db9876",
                    'description' => 'Количество клиентов, у которых один успешный платеж и статус абонемента - Отказался',
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

        $activeSubscriptionsCP = StatisticsModel::where('period_type', $request->get('period'))->where('product_id', $request->get('productId'))->where('type', StatisticsModel::TWELFTH_STATISTICS)->get()->pluck('value', 'key');
        $activeSubscriptionsDT = StatisticsModel::where('period_type', $request->get('period'))->where('product_id', $request->get('productId'))->where('type', StatisticsModel::FIFTEENTH_STATISTICS)->get()->pluck('value', 'key');
        $chats->push([
            'type' => 'highchart',
            'chart' => [
                'type' => 'area',
            ],
            "title" => ["text" => 'Активные абонементы.'],
            'xAxis' => [
                'type' => 'datetime',
            ],
            "series" => [
                [
                    'editable' => false,
                    "name" => "Cloudpayments (подписка)",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($activeSubscriptionsCP) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($activeSubscriptionsCP[$category] ?? 0)];
                    })->toArray()),
                    "color" => "#46a6cd6b",
                    'statisticsType' => StatisticsModel::TWELFTH_STATISTICS,
                    'description' => 'Тип оплаты: (cloudpayments) | Есть один платеж | Статус абонемента: (Оплачено, Жду оплату)',
                    'stacking' => 'normal'
                ],
                [
                    'editable' => false,
                    "name" => "Прямой перевод",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($activeSubscriptionsDT) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($activeSubscriptionsDT[$category] ?? 0)];
                    })->toArray()),
                    "color" => "#db9876",
                    'statisticsType' => StatisticsModel::FIFTEENTH_STATISTICS,
                    'description' => 'Тип оплаты: (прямой перевод) | Есть один платеж | Статус абонемента: (Оплачено, Жду оплату)',
                    'stacking' => 'normal'
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

        $eventsOfWeek = StatisticsModel::where('period_type', $request->get('period'))->where('product_id', $request->get('productId'))->where('type', StatisticsModel::EVENTS_OF_WEEK)->get()->pluck('value', 'key');

        $eventData = [];
        foreach ($categories as $key => $category) {
            $eventData[] = [
                'id' => $key,
                'key' => $category,
                'icon' => 'calendar',
                'status' => 'success',
                'title' => Carbon::parse($category / 1000)->isoFormat('DD MMM, YY'),
                'controls' => [
                    [
                        'method' => 'edit',
                        'icon' => 'edit',
                    ]
                ],
                'createdDate' => Carbon::parse($category / 1000)->isoFormat('YYYY-MM-DD'),
                'body' => $eventsOfWeek[$category] ?? '',
            ];
        }

        // dd($eventData);

        $chats->push([
            'type' => 'timeline',
            "series" => [
                [
                    'editable' => false,
                    "name" => "События недели",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($eventsOfWeek) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($eventsOfWeek[$category] ?? 0)];
                    })->toArray()),
                    'description' => '',
                    'statisticsType' => StatisticsModel::EVENTS_OF_WEEK,
                ],
            ],
            'items' => $eventData
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
            ->whereProductId($productId)
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
            ->whereProductId($productId)
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
            ->whereProductId($productId)
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
            'type' => 'highchart',
            'chart' => [
                'type' => 'area',
            ],
            "title" => ["text" => 'Рентабельность услуги'],
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

        $chats->push([
            'type' => 'highchart',
            'chart' => [
                'type' => 'area',
            ],
            "title" => ["text" => 'Доход по типу платежей'],
            "xAxis" => [
                "type" => 'datetime',
            ],
            "series" => [
                [
                    'editable' => false,
                    "name" => "Доход по переводам",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($turnoverTransfers) {
                        return ['stack' => 1, 'name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => isset($turnoverTransfers[$category]) ? $turnoverTransfers[$category]->sum('total') : 0];
                    })->toArray()),
                    "color" => "#e8bf29",
                    "description" => "Доход по переводам - Прямой перевод | Суммирование всех платежей (цена * количество)",
                    'groupPadding' => 0,
                    'stacking' => 'normal'
                ],
                [
                    'editable' => false,
                    "name" => "Доход по подпискам",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($turnoverCloudpayments) {
                        return ['stack' => 2, 'name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => isset($turnoverCloudpayments[$category]) ? $turnoverCloudpayments[$category]->sum('total') : 0];
                    })->toArray()),
                    "color" => "#2adaca",
                    "description" => "Доход по подпискам - Cloudpayments | Суммирование всех платежей (цена * количество)",
                    'groupPadding' => 0,
                    'stacking' => 'normal'
                ],
            ],
            'plotOptions' => [
                'area' => [
                    // 'stacking' => 'normal',
                    'fillOpacity' => 0.5,
                    'dataLabels' => [
                        'enabled' => true,
                    ],
                ],
                'column' => [
                    // 'grouping' => true,
                    'stacking' => 'normal',
                    'fillOpacity' => 2.5,
                    'dataLabels' => [
                        'enabled' => true,
                    ],
                ]
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

    public function updateTimeline(Request $request)
    {
        access(['can-operator', 'can-head', 'can-host']);

        $request->validate([
            "item" => "required",
            "productId" => "required",
            "period" => "required",
        ]);

        $item = $request->input('item');

        StatisticsModel::updateOrCreate([
            'period_type' => $request->get('period'),
            'product_id' => (int) $request->get('productId'),
            'type' => (int) $item['statisticsType'],
            'key' => $item['key'],
        ], [
            'value' => $item['data'],
        ]);

        return response()->json([
            'message' => 'Данные успешно сохранены.',
        ], 200);
    }
}
