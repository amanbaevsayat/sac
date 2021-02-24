<?php

namespace App\Http\Controllers;

use App\Models\Payment;
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

    public function statistics()
    {
        return view('pages.statistics');
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
