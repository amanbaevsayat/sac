<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerRequest;
use App\Http\Requests\CreateCustomerWithDataRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Filters\CustomerFilter;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerWithSubscription\CustomerResource as CustomerWithSubscriptionResource;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    private $root;
    private $perPage;

    public function __construct()
    {
        $this->root = 'customers';
        $this->perPage = 45;
    }

    public function getCustomerWithData($customerId)
    {
        $customer = Customer::whereId($customerId)->firstOr(function () use ($customerId) {
            throw new \Exception('Клиент не найден', 404);
        });

        return response()->json([
            'data' => new CustomerWithSubscriptionResource($customer),
            'message' => 'Успешно'
        ], 200);
    }

    public function getOptions()
    {
        $products = Product::all();
        $data = [];
        foreach ($products as $product) {
            $paymentTypes = $product->paymentTypes()->pluck('name')->toArray();
            if (! empty($paymentTypes)) {
                foreach ($paymentTypes as $paymentType) {
                    $data[$product->id][$paymentType] = [
                        'title' => Subscription::PAYMENT_TYPE[$paymentType],
                        'statuses' => [],
                    ];
        
                    switch ($paymentType) {
                        case 'tries':
                            $statuses = Subscription::STATUSES;
                            unset($statuses['paid']);
                            unset($statuses['rejected']);
                            $data[$product->id][$paymentType]['statuses'] = $statuses;
                            break;
                        case 'cloudpayments':
                            $statuses = Subscription::STATUSES;
                            unset($statuses['tries']);
                            $data[$product->id][$paymentType]['statuses'] = $statuses;
                            break;
                        case 'transfer':
                            $statuses = Subscription::STATUSES;
                            unset($statuses['tries']);
                            unset($statuses['frozen']);
                            unset($statuses['rejected']);
                            $data[$product->id][$paymentType]['statuses'] = $statuses;
                            break;
                        case 'simple_payment':
                            $statuses = Subscription::STATUSES;
                            unset($statuses['tries']);
                            unset($statuses['frozen']);
                            $data[$product->id][$paymentType]['statuses'] = $statuses;
                            break;
                    }
                }
            }
        }

        $users = User::all()->pluck('account', 'id')->toArray();

        $userRole = Auth::user()->getRole();

        if (Auth::user()->teams->count() > 0) {
            $userTeamIds = Auth::user()->teams->pluck('id');
        } else {
            $userTeamIds = [];
        }

        return response()->json([
            'quantities' => Payment::QUANTITIES,
            'paymentTypes' => $data,
            'users' => $users,
            'user' => Auth::id(),
            'userRole' => $userRole,
            'userTeamIds' => $userTeamIds,
        ], 200);
    }

    public function createWithData(CreateCustomerWithDataRequest $request)
    {
        $data = $request->all();
        try {
            $customer = $this->getCustomer($data);
        } catch (\Throwable $e) {
            \Log::info($e->getMessage());
            \Log::info('Ошибка getCustomer(). User ID: ' . Auth::id() . '. Phone: ' . ($data['customer']['phone'] ?? null) . '. ID: ' . ($data['customer']['id'] ?? null));
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'customer.phone' => [
                        'Клиент с таким номером уже существует.'
                    ],
                ]
            ], 422);
        }

        foreach ($data['subscriptions'] as $key => $item) {
            if ($item['status'] == 'refused' && ! $item['reason_id']) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        'subscriptions.' . $key . '.reason_id' => [
                            'Укажите причину отказа'
                        ],
                    ]
                ], 422);
            }
            $subscription = $customer->subscriptions()->where('product_id', $item['product_id'])->first();
            $endedAt = Carbon::parse($item['ended_at']);
            $triesAt = Carbon::parse($item['tries_at']);

            $subscription = Subscription::updateOrCreate([
                'product_id' => $item['product_id'],
                'customer_id' => $customer->id,
            ], [
                'customer_id' => $customer->id,
                'price' => $item['price'],
                'payment_type' => $item['payment_type'],
                'started_at' => Carbon::parse($item['started_at']),
                'ended_at' => $endedAt,
                'tries_at' => $triesAt,
                'status' => $item['status'],
                'reason_id' => $item['reason_id'],
            ]);

            // Если абонемент создается
            if ($subscription->wasRecentlyCreated) {
                // Если у оператора есть команда
                if (Auth::user()->teams->count() > 0) {
                    $subscription->update([
                        'team_id' => Auth::user()->teams->random()->id,
                    ]);
                }

                $subscription->update([
                    'tries_at' => Carbon::parse($item['tries_at']),
                ]);
            }

            if ($subscription->payment_type == 'cloudpayments') {
                // Если оператор изменил дату следующего платежа, то делаем запрос в cp, на изменения даты
            } elseif ($subscription->payment_type == 'transfer') {
                if (isset($item['newPayment']['check'])) {
                    $payment = $subscription->payments()->create([
                        'customer_id' => $customer->id,
                        'product_id' => $subscription->product->id,
                        'user_id' => Auth::id(),
                        'type' => 'transfer',
                        'status' => 'Completed',
                        'quantity' => $item['newPayment']['quantity'] ?? 1,
                        'amount' => $subscription->price,
                        'paided_at' => Carbon::now(),
                        'data' => [
                            'check' => $item['newPayment']['check'],
                            'subscription' => [
                                'renewed' => true,
                                'from' => $item['newPayment']['from'],
                                'to' => $item['newPayment']['to'],
                            ],
                        ],
                    ]);

                    $payment->subscription()->update([
                        'status' => 'paid',
                    ]);
                }
            }
        }

        return response()->json([
            'customer' => new CustomerWithSubscriptionResource($customer),
            'message' => 'Клиент успешно создан или обновлен'
        ], 200);
    }

    private function getCustomer(array $data): Customer
    {
        $customerExists = Customer::where('id', ($data['customer']['id'] ?? null))->where('phone', $data['customer']['phone'])->exists();
        $updateCustomer = isset($data['customer']['id']);
        
        if ($updateCustomer) { // Обновить клиента
            if ($customerExists) { // Обновить существующего клиента
                $customer = Customer::updateOrCreate([
                    'id' => $data['customer']['id'],
                    'phone' => $data['customer']['phone'],
                ], [
                    'name' => $data['customer']['name'],
                    'email' => $data['customer']['email'],
                    'comments' => $data['customer']['comments'],
                ]);
            } else if (Customer::where('phone', $data['customer']['phone'])->exists()) { // Изменил phone на существующий
                throw new \Exception('Клиент с таким номером уже существует.');
            } else if (Customer::withTrashed()->where('phone', $data['customer']['phone'])->exists()) { // Изменил phone на удаленного клиента
                Customer::withTrashed()->where('phone', $data['customer']['phone'])->forceDelete();
                $customer = Customer::withTrashed()->updateOrCreate([
                    'id' => $data['customer']['id'],
                ], [
                    'phone' => $data['customer']['phone'],
                    'name' => $data['customer']['name'],
                    'email' => $data['customer']['email'],
                    'comments' => $data['customer']['comments'],
                    'deleted_at' => null,
                ]);
            } else {
                $customer = Customer::updateOrCreate([
                    'id' => $data['customer']['id'],
                ], [
                    'phone' => $data['customer']['phone'],
                    'name' => $data['customer']['name'],
                    'email' => $data['customer']['email'],
                    'comments' => $data['customer']['comments'],
                ]);
            }
        } else { // Создать клиента
            if (Customer::where('phone', $data['customer']['phone'])->exists()) { // Создал phone на существующий
                throw new \Exception('Клиент с таким номером уже существует.');
            } else if (Customer::withTrashed()->where('phone', $data['customer']['phone'])->exists()) { // Создал phone на удаленного клиента
                Customer::withTrashed()->where('phone', $data['customer']['phone'])->forceDelete();
                $customer = Customer::withTrashed()->updateOrCreate([
                    'phone' => $data['customer']['phone'],
                ], [
                    'name' => $data['customer']['name'],
                    'email' => $data['customer']['email'],
                    'comments' => $data['customer']['comments'],
                    'deleted_at' => null,
                ]);
            } else {
                // Создать или обновить клиента, если есть телефон
                $customer = Customer::updateOrCreate([
                    'phone' => $data['customer']['phone'],
                ], [
                    'name' => $data['customer']['name'],
                    'email' => $data['customer']['email'],
                    'comments' => $data['customer']['comments'],
                ]);
            }
        }

        return $customer;
    }

    public function getList(CustomerFilter $filters)
    {
        access(['can-operator', 'can-head', 'can-host']);

        $query = Customer::query();
        $customers = $query->latest()->filter($filters)->paginate($this->perPage)->appends(request()->all());

        return response()->json(new CustomerCollection($customers), 200);
    }

    public function getFilters()
    {
        access(['can-operator', 'can-head', 'can-host']);

        $data['main'] = [
            [
                'name' => 'email',
                'title' => 'E-mail',
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

        return view("{$this->root}.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateCustomerRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCustomerRequest $request, Customer $customer)
    {
        access(['can-operator', 'can-head', 'can-host']);

        $customer->create($request->all());
        return redirect()->route("{$this->root}.index")->with('success', 'Клиент успешно создан.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        access(['can-operator', 'can-head', 'can-host']);

        return view("{$this->root}.show", [
            'customer' => $customer,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        access(['can-operator', 'can-head', 'can-host']);

        return view("{$this->root}.edit", [
            'customer' => $customer,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CreateCustomerRequest $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(CreateCustomerRequest $request, Customer $customer)
    {
        access(['can-operator', 'can-head', 'can-host']);
        $customer->update($request->all());

        $message = 'Данные клиента успешно изменены.';
        if ($request->ajax()) {
            return response()->json([
                'message' => $message,
            ]);
        } else {
            return redirect()->to(route("{$this->root}.show", [$customer->id]))->with('success', $message);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        access(['can-operator', 'can-head', 'can-host']);

        $customer->delete();
        return redirect()->route("{$this->root}.index")->with('success', 'Клиент успешно удален.');
    }
}
