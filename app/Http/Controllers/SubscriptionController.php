<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSubscriptionRequest;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Filters\SubscriptionFilter;
use App\Http\Resources\SubscriptionCollection;
use App\Models\Customer;
use App\Models\Product;

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
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

        $query = Subscription::query();
        $subscriptions = $query->filter($filters)->paginate($this->perPage)->appends(request()->all());

        return response()->json(new SubscriptionCollection($subscriptions), 200);
    }

    public function getFilters()
    {
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);
        $customers = Customer::get()->pluck('name_with_phone', 'id');
        $products = Product::get()->pluck('title', 'id');
        $data = [
            [
                'name' => 'customer_id',
                'title' => 'Клиенты',
                'type' => 'select',
                'options' => $customers,
            ],
            [
                'name' => 'product_id',
                'title' => 'Услуги',
                'type' => 'select',
                'options' => $products,
            ],
            [
                'name' => 'payment_type',
                'title' => 'Тип',
                'type' => 'select',
                'options' => Subscription::PAYMENT_TYPE,
            ],
            [
                'name' => 'status',
                'title' => 'Метка',
                'type' => 'select',
                'options' => Subscription::STATUSES,
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
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

        return view("{$this->root}.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);
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
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

        $subscription->create($request->all());
        return redirect()->route("{$this->root}.index")->with('success', 'Подписка успешно создана.');
    }

    /**
     * Display the specified resource.
     *
     * @param Subscription $subscription
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Subscription $subscription)
    {
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

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
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);
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
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);
        $subscription->update($request->all());

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
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subscription $subscription)
    {
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

        $subscription->delete();
        return redirect()->route("{$this->root}.index")->with('success', 'Подписка успешно удалена.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);
        $id = $request->get('id');
        $subscription = Subscription::whereId($id)->firstOr(function () use ($id) {
            throw new \Exception('Абонемент не найден. ID: ' . $id, 404);
        });
        $subscription->delete();
        return response()->json([
            'message' => 'Абонемент успешно удален.'
        ], 200);
    }
}
