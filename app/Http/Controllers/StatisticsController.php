<?php

namespace App\Http\Controllers;

use App\Models\Chart;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Reason;
use App\Models\StatisticsModel;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
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
        $period = $request->input('period');
        $product = Product::whereId($productId)->firstOrFail();

        $eventsOfWeek = StatisticsModel::where('period_type', $request->get('period'))->where('product_id', $request->get('productId'))->where('graph_id', StatisticsModel::EVENTS_OF_WEEK)->get()->pluck('value', 'key');

        $eventData = [];
        foreach ($categories as $key => $category) {
            $label = Str::limit(strip_tags(html_entity_decode($eventsOfWeek[$category] ?? '')), $limit = 70, $end = '...');
            $eventData[] = [
                'x' => $category,
                // 'name' => 'name',
                'name' => $this->getTimelineName($request->get('period'), $category),
                'label' => $label,
                // 'title' => 'title',
                'description' => $eventsOfWeek[$category] ?? '',
            ];
        }

        $data[0] = [
            'type' => 'highchart',
            'chart' => [
                'zoomType' => 'x',
                'type' => 'timeline'
            ],
            'xAxis' => [
                'type' => 'datetime',
                'visible' => false
            ],
            'yAxis' => [
                'visible' => false,
            ],
            'legend' => [
                'enabled' => false
            ],
            'title' => [
                'text' => 'События недели'
            ],
            'tooltip' => [
                'style' => [
                    'width' => 400
                ],
                'useHTML' => true,
            ],
            'series' => [
                [
                    'statisticsType' => StatisticsModel::EVENTS_OF_WEEK,
                    'dataLabels' => [
                        'connectorWidth' => 3,
                    ],
                    // 'marker' => [
                    //     'symbol' => 'circle'
                    // ],
                    'data' => $eventData
                ],
            ],
        ];

        $charts = $product->charts->where('type', Chart::TYPE_QUANTITATIVE);

        foreach ($charts as $chart) {
            $data[$chart->id] = [
                'type' => 'highchart',
                'chart' => [
                    'type' => 'area',
                ],
                'title' => ['text' => $chart->title],
                'xAxis' => [
                    'type' => 'datetime',
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

            if ($chart->is_stacking) {
                $data[$chart->id]['plotOptions']['area']['stacking'] = 'normal';
            }

            foreach ($chart->graphs as $graph) {
                $statistics = $graph->statistics()->where('product_id', $product->id)->where('period_type', $period)->get()->pluck('value', 'key');

                $data[$chart->id]['series'][] = [
                    'name' => $graph->name,
                    'productId' => $productId,
                    'description' => $graph->description,
                    'color' => $graph->color,
                    'editable' => !! $graph->is_editable,
                    'visible' => !! $graph->is_visible,
                    'statisticsType' => $graph->id,
                    'data' => array_values(collect($categories)->map(function ($category, $key) use ($statistics) {
                        return [
                            'name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'),
                            'x' => $category,
                            'y' => (int) ($statistics[$category] ?? 0),
                        ];
                    })->toArray()),
                ];
            }
        }

        $reasons = Subscription::join('reasons', 'subscriptions.reason_id', '=', 'reasons.id')
            ->select(
                'reasons.title',
                \DB::raw('DATE(subscriptions.updated_at + INTERVAL (8 - DAYOFWEEK(subscriptions.updated_at)) DAY) as date'),
                \DB::raw("COUNT(subscriptions.id) as total")
                // \DB::raw('DATEADD(DAY, 2 - DATEPART(WEEKDAY, subscriptions.updated_at), CAST(subscriptions.updated_at AS DATE))')
            )
            ->where('subscriptions.reason_id', '!=', null)
            ->where('subscriptions.product_id', $product->id)
            ->whereBetween('subscriptions.updated_at', [$from, $to])
            ->groupBy(['subscriptions.product_id', 'reasons.title', 'subscriptions.updated_at'])
            ->get()
            ->groupBy('title')->transform(function($items, $k) {
                return $items->groupBy(function ($item, $key) {
                    return (int) Carbon::parse($item->date)->setTimezone('Asia/Almaty')->endOfWeek()->startOfDay()->valueOf();
                })->transform(function ($items, $k) {
                    return $items->count();
                });
            })->toArray();

        $reasonsSeries = [];
        
        foreach ($reasons as $key => $reason) {
            $reasonData = [];

            foreach ($reason as $date => $total) {
                $reasonData[] = [
                    'y' => $total,
                    'x' => $date,
                ];
            }

            $reasonsSeries[] = [
                'name' => $key,
                'data' => $reasonData,
            ];
        }

        $data['reason'] = [
            'type' => 'highchart',
            'chart' => [
                'type' => 'column'
            ],
            'title' => [
                'text' => 'Причины отказов'
            ],
            // 'subtitle' => [
            //     'text' => 'Source => WorldClimate.com'
            // ],
            'xAxis' => [
                'type' => 'datetime',
                // 'categories' => $categories,
                'crosshair' => true
            ],
            'yAxis' => [
                'min' => 0,
                'title' => [
                    'text' => 'Количество отказов'
                ]
            ],
            'tooltip' => [
                'headerFormat' => '<table>',
                'pointFormat' => '<tr><td style="color:{series.color};padding:0">{series.name}: </td><td style="padding:0"><b> {point.y}</b></td></tr>',
                'footerFormat' => '</table>',
                'shared' => true,
                'useHTML' => true
            ],
            "series" => $reasonsSeries,
            'plotOptions' => [
                'column' => [
                    'pointPadding' => 0,
                    'borderWidth' => 0
                ],
            ],
        ];

        $chats = $data;
        return view('pages.statistics', compact('products', 'chats'));
    }

    private function getTimelineName($period, $category)
    {
        $start = $period == 'week' ? 
            Carbon::parse($category / 1000)->startOfWeek()->isoFormat('DD') : 
            Carbon::parse($category / 1000)->startOfMonth()->isoFormat('DD');
        $end = $period == 'week' ? 
            Carbon::parse($category / 1000)->endOfWeek()->isoFormat('DD MMM, YY') : 
            Carbon::parse($category / 1000)->endOfMonth()->isoFormat('DD MMM, YY');
        return $start . ' - ' . $end;
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
        
        $turnovers = \DB::table('payments')
            ->select('payments.*' ,\DB::raw('quantity * amount as total'))
            ->whereProductId($productId)
            ->whereNull('deleted_at')
            ->where('type', '!=', 'tries')
            ->whereStatus('Completed')
            ->whereBetween('paided_at', [$from, $to])
            ->get()
            ->groupBy('type')
            ->transform(function($item, $k) use ($period) {
                return $item->groupBy(function($payment) use ($period) {
                    if ($period === StatisticsModel::PERIOD_TYPE_MONTH) {
                        return (int) Carbon::parse($payment->paided_at)->endOfMonth()->startOfDay()->valueOf();
                    } else if ($period === StatisticsModel::PERIOD_TYPE_WEEK) {
                        return (int) Carbon::parse($payment->paided_at)->endOfWeek()->startOfDay()->valueOf();
                    }
                });
            })->toArray();

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
                'label' => [
                    'style' => [
                        'color' => '#000',
                    ],
                ],
            ],
            "series" => [
                [
                    'editable' => false,
                    "name" => "Доход по переводам",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($turnovers) {
                        return ['stack' => 1, 'name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => isset($turnovers['transfer'][$category]) ? collect($turnovers['transfer'][$category])->sum('total') : 0];
                    })->toArray()),
                    "color" => "#e8bf29",
                    "description" => "Доход по переводам - Прямой перевод | Суммирование всех платежей (цена * количество)",
                    'groupPadding' => 0,
                    // 'stacking' => 'normal'
                ],
                [
                    'editable' => false,
                    "name" => "Доход по подпискам",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($turnovers) {
                        return ['stack' => 2, 'name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => isset($turnovers['cloudpayments'][$category]) ? collect($turnovers['cloudpayments'][$category])->sum('total') : 0];
                    })->toArray()),
                    "color" => "#2adaca",
                    "description" => "Доход по подпискам - Cloudpayments | Суммирование всех платежей (цена * количество)",
                    'groupPadding' => 0,
                    // 'stacking' => 'normal'
                ],
                [
                    'editable' => false,
                    "name" => "Доход по разовым платежам",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($turnovers) {
                        return ['stack' => 2, 'name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => isset($turnovers['simple_payment'][$category]) ? collect($turnovers['simple_payment'][$category])->sum('total') : 0];
                    })->toArray()),
                    "color" => "#c2de80",
                    "description" => "Доход по разовым платежам - Cloudpayments | Суммирование всех платежей (цена * количество)",
                    'groupPadding' => 0,
                    // 'stacking' => 'normal'
                ],
            ],
            'plotOptions' => [
                'area' => [
                    'stacking' => 'normal',

                    'fillOpacity' => 0.5,
                    'dataLabels' => [
                        'enabled' => true,
                        'color' => '#000',
                        'className' => 'temirlan',
                    ],
                    'label' => [
                        'style' => [
                            'color' => '#000'
                        ]
                    ]
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
                'graph_id' => (int) $request->input('item.statisticsType'),
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
            'graph_id' => (int) $item['statisticsType'],
            'key' => $item['key'],
        ], [
            'value' => $item['data'],
        ]);

        return response()->json([
            'message' => 'Данные успешно сохранены.',
        ], 200);
    }
}
