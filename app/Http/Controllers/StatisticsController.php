<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Product;
use App\Models\Statistics;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class StatisticsController extends Controller
{
    private function getSundays($from, $to)
    {
        $no = 0;
        $start = new \DateTime($from);
        $end   = new \DateTime($to);
        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($start, $interval, $end);
        $data = [];
        foreach ($period as $dt)
        {
            if ($dt->format('N') == 7)
            {
                $data[] = (int) Carbon::parse($dt)->startOfDay()->valueOf();
                $no++;
            }
        }
        return $data;
    }

    public function quantitative(Request $request)
    {
        access(['can-operator', 'can-head', 'can-host']);
        if (!$request->has('from') && !$request->has('to')) {
            return redirect()->route('statistics.quantitative', [
                "productId" => $request->input('productId') ?? Product::first()->id ?? null,
                "from" => $request->input('from') ?? Carbon::now()->subMonth()->format('Y-m-d'),
                "to" => $request->input('to') ?? Carbon::now()->format('Y-m-d'),
            ]);
        }

        $products = Product::get()->pluck('title', 'id');

        $request->validate([
            "from" => "required|date_format:Y-m-d",
            "to" => "required|date_format:Y-m-d",
            "productId" => "required",
        ]);

        
        $chats = collect();
        $from = Carbon::createFromFormat('Y-m-d', $request->input('from'), 'Asia/Almaty')->startOfDay()->setTimezone('Asia/Almaty');
        $to = Carbon::createFromFormat('Y-m-d', $request->input('to'), 'Asia/Almaty')->endOfDay()->setTimezone('Asia/Almaty');
        $categories = $this->getSundays($from, $to);
        $productId = $request->input('productId');

        $newLeadsFirst = Statistics::where('product_id', $request->get('productId'))->where('type', Statistics::FIRST_STATISTICS)->get()->pluck('value', 'key');
        $newLeadsSecond = Statistics::where('product_id', $request->get('productId'))->where('type', Statistics::SECOND_STATISTICS)->get()->pluck('value', 'key');
        
        $chats->push([
            "title" => ["text" => 'Новые лиды'],
            "xAxis" => [
                "type" => 'linear',
            ],
            "series" => [
                [
                    'editable' => false,
                    'statisticsType' => Statistics::SECOND_STATISTICS,
                    "name" => "Новые клиенты",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($newLeadsSecond) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($newLeadsSecond[$category] ?? 0)];
                    })->toArray()),
                    "color" => "#c2de80",
                ],
                [
                    'editable' => true,
                    'statisticsType' => Statistics::FIRST_STATISTICS,
                    "name" => "Новые лиды (Instagram)",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($newLeadsFirst) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($newLeadsFirst[$category] ?? 0)];
                    })->toArray()),
                    "color" => "#db9876",
                ],
            ]
        ]);

        $outflowClients = Statistics::where('product_id', $request->get('productId'))->where('type', Statistics::THIRD_STATISTICS)->get()->pluck('value', 'key');
        $outflowTrials = Statistics::where('product_id', $request->get('productId'))->where('type', Statistics::FOURTH_STATISTICS)->get()->pluck('value', 'key');

        $chats->push([
            "title" => ["text" => 'Динамика базы клиентов'],
            "xAxis" => [
                "type" => 'linear',
            ],
            "series" => [
                [
                    'editable' => false,
                    "name" => "Приток клиентов",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($newLeadsSecond) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($newLeadsSecond[$category] ?? 0)];
                    })->toArray()),
                    "color" => "#c2de80",
                ],
                [
                    'editable' => false,
                    "name" => "Отток клиентов",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($outflowClients) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($outflowClients[$category] ?? 0)];
                    })->toArray()),
                    "color" => "#db9876",
                ],
                [
                    'editable' => false,
                    "name" => "Отток пробных",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($outflowTrials) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => (int) ($outflowTrials[$category] ?? 0)];
                    })->toArray()),
                    "color" => "#2adaca",
                ],
            ]
        ]);
        return view('pages.statistics', compact('products', 'chats'));
    }

    public function financial(Request $request)
    {
        access(['can-operator', 'can-head', 'can-host']);
        if (!$request->has('from') && !$request->has('to')) {
            return redirect()->route('statistics.financial', [
                "productId" => $request->input('productId') ?? Product::first()->id ?? null,
                "from" => $request->input('from') ?? Carbon::now()->subMonth()->format('Y-m-d'),
                "to" => $request->input('to') ?? Carbon::now()->format('Y-m-d'),
            ]);
        }

        $products = Product::get()->pluck('title', 'id');

        $request->validate([
            "from" => "required|date_format:Y-m-d",
            "to" => "required|date_format:Y-m-d",
            "productId" => "required",
        ]);

        
        $chats = collect();
        $from = Carbon::createFromFormat('Y-m-d', $request->input('from'), 'Asia/Almaty')->startOfDay()->setTimezone('Asia/Almaty');
        $to = Carbon::createFromFormat('Y-m-d', $request->input('to'), 'Asia/Almaty')->endOfDay()->setTimezone('Asia/Almaty');
        $categories = $this->getSundays($from, $to);
        $productId = $request->input('productId');
        $turnover = \DB::table('payments')
            ->select('payments.*' ,\DB::raw('quantity * amount as total'))
            ->whereStatus('Completed')
            ->whereBetween('paided_at', [$from, $to])
            ->get()
            ->groupBy(function($payment) {
                return (int) Carbon::parse($payment->paided_at)->endOfWeek()->startOfDay()->valueOf();
            });
            // dd($turnover);
            // ->value(\DB::raw("SUM(amount * quantity)"));
        $chats->push([
            "title" => ["text" => 'Оборот'],
            "xAxis" => [
                "type" => 'linear',
            ],
            "series" => [
                [
                    'editable' => false,
                    "name" => "Оборот по услуге за неделю",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($turnover) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => isset($turnover[$category]) ? $turnover[$category]->sum('total') : 0];
                    })->toArray()),
                    "color" => "#c2de80",
                ],
            ]
        ]);

        $turnoverCloudpayments = \DB::table('payments')
            ->select('payments.*' ,\DB::raw('quantity * amount as total'))
            ->whereType('cloudpayments')
            ->whereStatus('Completed')
            ->whereBetween('paided_at', [$from, $to])
            ->get()
            ->groupBy(function($payment) {
                return (int) Carbon::parse($payment->paided_at)->endOfWeek()->startOfDay()->valueOf();
            });
        
        $turnoverTransfers = \DB::table('payments')
            ->select('payments.*' ,\DB::raw('quantity * amount as total'))
            ->whereType('transfer')
            ->whereStatus('Completed')
            ->whereBetween('paided_at', [$from, $to])
            ->get()
            ->groupBy(function($payment) {
                return (int) Carbon::parse($payment->paided_at)->endOfWeek()->startOfDay()->valueOf();
            });
            // dd($turnover);
            // ->value(\DB::raw("SUM(amount * quantity)"));

        $chats->push([
            "title" => ["text" => 'Виды оплаты'],
            "xAxis" => [
                "type" => 'linear',
            ],
            "series" => [
                [
                    'editable' => false,
                    "name" => "Оборот по переводам",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($turnoverTransfers) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => isset($turnoverTransfers[$category]) ? $turnoverTransfers[$category]->sum('total') : 0];
                    })->toArray()),
                    "color" => "#c2de80",
                ],
                [
                    'editable' => false,
                    "name" => "Оборот по подпискам",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($turnoverCloudpayments) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => isset($turnoverCloudpayments[$category]) ? $turnoverCloudpayments[$category]->sum('total') : 0];
                    })->toArray()),
                    "color" => "#2adaca",
                ],
            ]
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
        ]);
        foreach ($request->input('item.data') as $item) {
            Statistics::updateOrCreate([
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
