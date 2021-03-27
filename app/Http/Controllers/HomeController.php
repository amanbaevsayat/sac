<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Product;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Artisan;

class HomeController extends Controller
{
    private $githash;

    function __construct()
    {
        $this->githash = env('GIT_HASH');
    }

    public function homepage()
    {
        return view('home');
    }

    public function thankYou()
    {
        return view('thank-you');
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function pull(Request $request)
    {
        $data = $request->validate([
            'githash' => 'required|string',
            'branch' => 'required|string|in:staging,master',
        ]);

        if ($data["githash"] != $this->githash) {
            return response()->with(["error" => "Hash is invalid"]);
        }

        $branch = $data["branch"];

        Artisan::call("git:pull --branch={$branch}");

        return response()->json([
            'status' => true
        ]);
    }

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
                // $date = Carbon::parse($dt)->setTimezone('UTC');
                // dd(Carbon::createFromFormat('Y-m-d H:i:s', $date, 'Asia/Almaty')->startOfDay()->setTimezone('Asia/Almaty'));
                // dd($date);
                // dd(Carbon::parse($dt)->startOfDay());
                $data[] = (int) Carbon::parse($dt)->startOfDay()->valueOf();
                $no++;
            }
        }
        return $data;
    }

    public function statistics(Request $request)
    {
        access(['can-operator', 'can-head', 'can-host']);
        if (!$request->has('from') && !$request->has('to')) {
            return redirect()->route('statistics.first', [
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
        $newLeadsSecond = Subscription::
            whereProductId($productId)
            ->whereHas('payments', function ($q) use ($from, $to) {
                $q->where('status', 'Completed')->whereBetween('paided_at', [$from, $to]);
            })->get()
            ->groupBy(function($subscription) {
                $firstCompletePayment = $subscription->payments->where('status', 'Completed')->sortBy('paided_at')->first();

                return (int) Carbon::parse($firstCompletePayment->paided_at)->endOfWeek()->startOfDay()->valueOf(); // grouping by years
            })->toArray();
            // dd($categories);
            // dd(Carbon::createFromTimestamp($categories[0])->toDateTimeString());
        
        // dd($categories, $newLeadsSecond);
        $chats->push([
            "title" => ["text" => 'Новые лиды'],
            "xAxis" => [
                "type" => 'linear',
            ],
            "series" => [
                [
                    'editable' => false,
                    "name" => "Новые клиенты",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($newLeadsSecond) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' =>count($newLeadsSecond[$category] ?? [])];
                    })->toArray()),
                    "color" => "#c2de80",
                ],
                [
                    'editable' => true,
                    'type' => 1,
                    "name" => "Новые лиды (Instagram)",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($newLeadsSecond) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' =>0];
                    })->toArray()),
                    "color" => "#db9876",
                ],
            ]
        ]);

        $outflowClients = Subscription::
            whereProductId($productId)
            ->whereHas('payments', function ($q) {
                $q->where('status', 'Completed');
            })
            ->whereStatus('refused')
            ->get()
            ->groupBy(function($subscription) {
                return (int) Carbon::parse($subscription->updated_at)->endOfWeek()->startOfDay()->valueOf(); // grouping by years
            })->toArray();

        $outflowTrials = Subscription::
            whereProductId($productId)
            ->whereDoesntHave('payments', function($q) {
                $q->where('status', 'Completed');
            })
            ->whereStatus('refused')
            ->get()
            ->groupBy(function($subscription) {
                return (int) Carbon::parse($subscription->updated_at)->endOfWeek()->startOfDay()->valueOf(); // grouping by years
            })->toArray();

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
                        // dd($category, $newLeadsSecond);
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => count($newLeadsSecond[$category] ?? [])];
                    })->toArray()),
                    "color" => "#c2de80",
                ],
                [
                    'editable' => false,
                    "name" => "Отток клиентов",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($outflowClients) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => count($outflowClients[$category] ?? [])];
                    })->toArray()),
                    "color" => "#db9876",
                ],
                [
                    'editable' => false,
                    "name" => "Отток пробных",
                    "data" => array_values(collect($categories)->map(function ($category, $key) use ($outflowTrials) {
                        return ['name' => Carbon::parse((int) $category / 1000)->setTimezone('Asia/Almaty')->isoFormat('DD MMM, YY'), 'x' => $category, 'y' => count($outflowTrials[$category] ?? [])];
                    })->toArray()),
                    "color" => "#2adaca",
                ],
            ]
        ]);
        return view('pages.statistics', compact('products', 'chats'));
    }

    public function statisticsSecond(Request $request)
    {
        access(['can-operator', 'can-head', 'can-host']);
        if (!$request->has('from') && !$request->has('to')) {
            return redirect()->route('statistics.second', [
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

    public function statisticsPost(Request $request)
    {
        $from = Carbon::parse($request->input('dates.from'))->format('Y-m-d 00:00:00');
        $to = Carbon::parse($request->input('dates.to'))->format('Y-m-d 23:59:59');
        $now = Carbon::now()->toDateString();
        $turnover = Payment::whereStatus('Completed')->whereBetween('paided_at', [$from, $to])->value(\DB::raw("SUM(amount * quantity)"));

        $data = [
            "trials" => [
                "leads" => $request->input('trials.leads'),
                "trial" => $request->input('trials.trial'),
                // Кол-во новых подключений
                "new" => Subscription::trial()->whereBetween('started_at', [$from, $to])
                    // ->whereDoesntHave('payments', function (Builder $query) {
                    //     $query->where('status', 'Completed');
                    // })
                    ->count(),
                "leadfeed_to_whatsap" => null, // formula
                // Кол-во должников за период
                "deptors" => Subscription::trial()->whereBetween('started_at', [$from, $to])
                    ->where('status', '!=', 'refused')
                    ->whereRaw('CASE WHEN tries_at > ended_at THEN tries_at ELSE ended_at END < ?', Carbon::now()->format('Y-m-d 23:59:59'))
                    ->count(),
                // Кол-во оплат за период от новичков
                "from_beginners" => Subscription::client()->whereBetween('started_at', [$from, $to])
                    ->whereHas('payments', function (Builder $query) {
                        $query->where('status', 'Completed');
                    })
                    ->count(),
                "trial_to_customers" => null, // formula
                // Кол-во отказавшихся за период пробных
                "refused_during_trial_period" => Subscription::whereBetween('started_at', [$from, $to])
                    ->where('status', 'refused')
                    ->count(),
            ],
            "customers" => [
                "clients" => $request->input('customers.clients'),
                // Кол-во должников
                "debtors" => Subscription::client()->deptors()
                    ->count(),
                "second_subscription" => Subscription::whereHas('payments', function (Builder $query) {
                    $query->where('status', 'Completed');
                }, '=', 2)->whereHas('payments', function (Builder $query) use ($from, $to) {
                    $query->where('status', 'Completed')->whereBetween('paided_at', [$from, $to]);
                }, '>', 0)->count(),
                "first_subscription" => Subscription::whereHas('payments', function (Builder $query) use ($from, $to) {
                    $query->where('status', 'Completed')->whereBetween('paided_at', [$from, $to]);
                }, '=', 1)->count(),
                "conversion_after_month" => null,
                "old" => null,
                // Кол-во отказавшихся клиентов
                "refused" => Subscription::client()->whereStatus('refused')->count(),
                "new" => null,
            ],
            "financial" => [
                "turnover" => number_format($turnover, 2),
                "advertising_costs" => $request->input('financial.advertising_costs'),
                "bonus_costs" => $request->input('financial.bonus_costs'),
                "shooting_costs" => $request->input('financial.shooting_costs'),
                "coach_costs" => $request->input('financial.coach_costs'),
            ],
            "payments" => [
                "total_payments" => null,
                "old" => null,
                "new" => null,
            ],
        ];

        return response()->json($data, 200);
    }
}
