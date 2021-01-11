<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerRequest;
use App\Http\Requests\CreateCustomerWithDataRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Filters\CustomerFilter;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\CustomerWithSubscription\CustomerResource as CustomerWithSubscriptionResource;
use App\Models\Subscription;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Str;
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
        $data = [];
        foreach (Subscription::PAYMENT_TYPE as $key => $paymentType) {
            $data[$key] = [
                'title' => $paymentType,
                'statuses' => [],
            ];

            switch ($paymentType) {
                case Subscription::PAYMENT_TYPE['tries']:
                    $statuses = Subscription::STATUSES;
                    unset($statuses['paid']);
                    $data[$key]['statuses'] = $statuses;
                    break;
                case Subscription::PAYMENT_TYPE['cloudpayments']:
                    $statuses = Subscription::STATUSES;
                    unset($statuses['tries']);
                    $data[$key]['statuses'] = $statuses;
                    break;
                case Subscription::PAYMENT_TYPE['transfer']:
                    $statuses = Subscription::STATUSES;
                    unset($statuses['tries']);
                    $data[$key]['statuses'] = $statuses;
                    break;
            }
        }

        return response()->json([
            'quantities' => Payment::QUANTITIES, 
            'paymentTypes' => $data, 
        ], 200);
    }

    public function createWithData(CreateCustomerWithDataRequest $request)
    {
        $data = $request->all();
        $type = '';
        if (isset($data['customer']['id']) && Customer::where('id', $data['customer']['id'])->where('phone', $data['customer']['phone'])->exists()) {
            // Update Client
            $customer = Customer::updateOrCreate([
                'id' => $data['customer']['id'], 
                'phone' => $data['customer']['phone'],
            ],[
                'name' => $data['customer']['name'],
                'email' => $data['customer']['email'],
                'comments' => $data['customer']['comments'],
            ]);
        } else if (!isset($data['customer']['id'])) {
            $type = 'create';
            // Create client or Update if isset phone
            $customer = Customer::updateOrCreate([
                'phone' => $data['customer']['phone'],
            ],[
                'name' => $data['customer']['name'],
                'email' => $data['customer']['email'],
                'comments' => $data['customer']['comments'],
            ]);
        } else {
            if (isset($data['customer']['id']) && Customer::where('id', '!=', $data['customer']['id'])->where('phone', $data['customer']['phone'])->exists()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        'customer.phone' => [
                                'Такое значение поля Телефон уже существует.'
                        ],
                    ]
                ], 422);
            }
            // Если у клиента сменился номер
            $customer = Customer::updateOrCreate([
                'id' => $data['customer']['id'], 
            ],[
                'phone' => $data['customer']['phone'],
                'name' => $data['customer']['name'],
                'email' => $data['customer']['email'],
                'comments' => $data['customer']['comments'],
            ]);
        }

        foreach ($data['subscriptions'] as $item) {
            $endedAt = Carbon::parse($item['ended_at']);
            $subscription = $customer->subscriptions()->where('product_id', $item['product_id'])->first();
            if ($subscription) {
                // Если оператор поменял статус на frozen, то создаем или обновляем платеж
                if ($subscription->status != 'frozen' && $item['status'] == 'frozen') {
                    $subscription->update([
                        'frozen_at' => Carbon::parse($item['frozen_at']),
                        'defrozen_at' => Carbon::parse($item['defrozen_at']),
                    ]);
                    if ($subscription->payments()->where('status', 'frozen')->where('type', 'frozen')->where('data->subscription->renewed', false)->doesntExist()) {
                        $subscription->payments()->create([
                            'customer_id' => $customer->id,
                            'user_id' => Auth::id(),
                            'type' => 'frozen',
                            'slug' => Str::uuid(),
                            'status' => 'frozen',
                            // 'recurrent' => true,
                            // 'amount' => $subscription->price,
                            // 'start_date' => $item['started_at'], // TODO
                            // 'interval' => 'Month',
                            // 'period' => 1,
                            'paided_at' => Carbon::now(),
                            'data' => [
                                'subscription' => [
                                    'renewed' => false,
                                    'from' => Carbon::parse($item['frozen_at']),
                                    'to' => Carbon::parse($item['defrozen_at']),
                                ],
                            ],
                        ]);
                    }
                }

                // Если оператор разморозил абонемент, то
                // 1) Находим платеж, меняем у него renewed на true
                // 2) Продлеваем абонемент
                if ($subscription->status == 'frozen' && $item['status'] != 'frozen') {
                    if ($payment = $subscription->payments()->where('status', 'frozen')->where('type', 'frozen')->where('data->subscription->renewed', false)->first()) {
                        $paymentData = $payment->data;
                        $from = Carbon::parse($paymentData['subscription']['from']);
                        $to = Carbon::parse($paymentData['subscription']['to']);

                        $diff = $from->diffInDays($to);
                        $endedAt = Carbon::parse($subscription->ended_at)->addDays($diff);

                        $paymentData['subscription']['renewed'] = true;

                        $payment->update([
                            'data' => $paymentData,
                        ]);
                    }
                }
            }

            $subscription = $customer->subscriptions()->updateOrCreate([
                'product_id' => $item['product_id'],
            ], [
                'price' => $item['price'],
                'payment_type' => $item['payment_type'],
                'started_at' => Carbon::parse($item['started_at']),
                'ended_at' => $endedAt,
                'status' => $item['status'],
            ]);

            if ($type == 'create') {
                $subscription->update([
                    'tries_at' => Carbon::parse($item['tries_at']),
                ]);
            }

            if ($subscription->payment_type == 'cloudpayments') {
                if ($subscription->payments()->where('status', 'new')->where('type', 'cloudpayments')->doesntExist()) {
                    $payment = $subscription->payments()->create([
                        'customer_id' => $customer->id,
                        'user_id' => Auth::id(),
                        'type' => 'cloudpayments',
                        'slug' => Str::uuid(),
                        'status' => 'new',
                        'recurrent' => true,
                        'amount' => $subscription->price,
                        'start_date' => $subscription->started_at ?? null, // TODO
                        'interval' => 'Month',
                        'period' => 1,
                        'paided_at' => Carbon::now(),
                    ]);
                }
            } elseif ($subscription->payment_type == 'transfer') {
                if (isset($item['newPayment']['check'])) {
                    $paymentStatus = $subscription->status == 'paid' ? 'Completed' : 'new';
                    if ($subscription->status == 'paid') {
                        // dd($item['newPayment']);
                        // $subscription->update([
                        //     'ended_at' => Carbon::parse($item['ended_at'])->addMonths($item['newPayment']['quantity']),
                        // ]);
                    }
    
                    $payment = $subscription->payments()->create([
                        'customer_id' => $customer->id,
                        'user_id' => Auth::id(),
                        'type' => 'transfer',
                        'slug' => Str::uuid(),
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
                }
            }
        }

        return response()->json([
            'customer' => new CustomerWithSubscriptionResource($customer),
            'message' => 'Клиент успешно создан или обновлен'
        ], 200);
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
