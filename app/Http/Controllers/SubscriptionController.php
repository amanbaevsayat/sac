<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSubscriptionRequest;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Filters\SubscriptionFilter;
use App\Http\Resources\SubscriptionCollection;
use App\Models\Customer;
use App\Models\Product;
use App\Models\UserLog;
use App\Services\CloudPaymentsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    private $root;
    private $perPage;

    public function __construct()
    {
        $this->root = 'subscriptions';
        $this->perPage = 45;
    }

    public function getList(SubscriptionFilter $filters)
    {
        access(['can-operator', 'can-head', 'can-host']);

        $query = Subscription::query();
        $subscriptions = $query->filter($filters)->latest()->paginate($this->perPage)->appends(request()->all());

        return response()->json(new SubscriptionCollection($subscriptions), 200);
    }

    public function getFilters()
    {
        access(['can-operator', 'can-head', 'can-host']);
        $products = Product::get()->pluck('title', 'id');
        $data['main'] = [
            // [
            //     'name' => 'customer_id',
            //     'title' => 'Клиенты',
            //     'type' => 'select-search',
            //     'key' => 'customer',
            //     'options' => [],
            // ],
            [
                'name' => 'product_id',
                'title' => 'Услуги',
                'type' => 'select-multiple',
                'options' => $products,
            ],
            [
                'name' => 'status',
                'title' => 'Статус абонемента',
                'type' => 'select-multiple',
                'options' => Subscription::STATUSES,
            ],
            [
                'name' => 'payment_type',
                'title' => 'Тип оплаты',
                'type' => 'select-multiple',
                'options' => Subscription::PAYMENT_TYPE,
            ],
            [
                'name' => 'from_start_date',
                'title' => 'С даты старта',
                'type' => 'date',
            ],
            [
                'name' => 'to_start_date',
                'title' => 'По дату старта',
                'type' => 'date',
            ],
            [
                'name' => 'cp_subscription_id',
                'title' => 'Cloudpayment ID',
                'type' => 'input',
            ],
            [
                'name' => 'id',
                'title' => 'ID абонемента',
                'type' => 'input',
            ],
        ];

        $data['second'] = [
            [
                'name' => 'customer_name_or_phone',
                'placeholder' => 'Найти по имени и номеру',
                'title' => 'Клиенты',
                'type' => 'input-search',
                'key' => 'customer',
                'options' => [],
            ],
        ];

        return response()->json($data, 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        access(['can-operator', 'can-head', 'can-host']);

        return view("{$this->root}.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        access(['can-operator', 'can-head', 'can-host']);
        $products = Product::get();
        $customers = Customer::get();

        return view("{$this->root}.create", [
            'products' => $products,
            'customers' => $customers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateSubscriptionRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSubscriptionRequest $request, Subscription $subscription)
    {
        access(['can-operator', 'can-head', 'can-host']);

        $subscription->create($request->all());
        return redirect()->route("{$this->root}.index")->with('success', 'Абонемент успешно создан.');
    }

    /**
     * Display the specified resource.
     *
     * @param Subscription $subscription
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Subscription $subscription)
    {
        access(['can-operator', 'can-head', 'can-host']);

        return view("{$this->root}.show", [
            'subscription' => $subscription,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Subscription  $subscriptionId
     * @return \Illuminate\Http\Response
     */
    public function edit(Subscription $subscription)
    {
        access(['can-operator', 'can-head', 'can-host']);
        $products = Product::get();
        $customers = Customer::get();

        return view("{$this->root}.edit", [
            'subscription' => $subscription,
            'products' => $products,
            'customers' => $customers,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CreateSubscriptionRequest $request
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function update(CreateSubscriptionRequest $request, Subscription $subscription)
    {
        access(['can-operator', 'can-head', 'can-host']);

        $subscription->update([
            'status' => $request->get('status'),
        ]);

        $message = 'Данные абонемента успешно изменены.';
        if ($request->ajax()) {
            return response()->json([
                'message' => $message,
            ]);
        } else {
            return redirect()->to(route("{$this->root}.show", [$subscription->id]))->with('success', $message);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Subscription $subscription
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subscription $subscription, Request $request)
    {
        access(['can-operator', 'can-head', 'can-host']);

        $subscription->delete();

        $message = 'Абонемент успешно удален.';
        if ($request->ajax()) {
            return response()->json([
                'message' => $message,
            ]);
        } else {
            return redirect()->route("{$this->root}.index")->with('success', $message);
        }
    }

    public function manualWriteOffPayment(Request $request)
    {
        $subscriptionId = $request->get('subscriptionId');
        $subscription = Subscription::whereId($subscriptionId)->whereNotNull('cp_subscription_id')->first();
        $now = Carbon::now();
        $endedAt = Carbon::parse($subscription->ended_at);
        if ($endedAt > $now) {
            throw new \Exception('Ошибка! Обратитесь к менеджеру или администратору.', 500);
        }

        $cloudpaymentService = new CloudPaymentsService();
        try {
            UserLog::create([
                'subscription_id' => $subscription->id,
                'user_id' => Auth::id(),
                'type' => UserLog::MANUAL_WRITE_OFF,
                'data' => [],
            ]);
            $cloudpaymentService->updateSubscription([
                'Id' => $subscription->cp_subscription_id,
                'StartDate' => Carbon::yesterday()->format('Y-m-d\TH:i:s.u'),
            ]);
        } catch (\Throwable $e) {
            throw new \Exception('Ошибка при запросе на ручное списание денег. Попробуйте позднее', 500);
        }

        return response()->json([
            'message' => 'Запрос на списание отправлен.'
        ], 200);
    }
}
