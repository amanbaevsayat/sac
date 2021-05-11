<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaymentRequest;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Filters\PaymentFilter;
use App\Http\Resources\PaymentCollection;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\UserLog;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    private $root;
    private $perPage;

    public function __construct()
    {
        $this->root = 'payments';
        $this->perPage = 45;
    }

    public function getList(PaymentFilter $filters)
    {
        access(['can-operator', 'can-head', 'can-host']);

        $query = Payment::query();
        $payments = $query->filter($filters)->orderBy('paided_at', 'DESC')->where('type', '!=', 'frozen')->where('status', '!=', 'new')->paginate($this->perPage)->appends(request()->all());

        return response()->json(new PaymentCollection($payments), 200);
    }

    public function getFilters()
    {
        access(['can-operator', 'can-head', 'can-host']);
        // $products = Product::get()->pluck('title', 'id');
        $paymentTypes = Subscription::PAYMENT_TYPE;
        unset($paymentTypes['tries']);
        unset($paymentTypes['frozen']);
        $products = Product::get()->pluck('title', 'id');

        $data['main'] = [
            [
                'name' => 'product_id',
                'title' => 'Услуги',
                'type' => 'select-multiple',
                'options' => $products,
            ],
            [
                'name' => 'type',
                'title' => 'Тип оплаты',
                'type' => 'select-multiple',
                'options' => $paymentTypes,
            ],
            [
                'name' => 'status',
                'title' => 'Статус платежа',
                'type' => 'select-multiple',
                'options' => Payment::STATUSES,
            ],
            [
                'name' => 'amount',
                'title' => 'Сумма',
                'type' => 'input',
            ],
            [
                'name' => 'from',
                'title' => 'С даты',
                'type' => 'date',
            ],
            [
                'name' => 'to',
                'title' => 'По дату',
                'type' => 'date',
            ],
            [
                'name' => 'id',
                'title' => 'ID',
                'type' => 'input',
            ],
            [
                'name' => 'transaction_id',
                'title' => 'Transaction ID',
                'type' => 'input',
            ],
            [
                'name' => 'newPayment',
                'title' => 'Только новые платежи',
                'type' => 'checkbox',
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
        $customers = Customer::get();
        $subscriptions = Subscription::get();

        return view("{$this->root}.create", [
            'subscriptions' => $subscriptions,
            'customers' => $customers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreatePaymentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreatePaymentRequest $request, Payment $payment)
    {
        access(['can-operator', 'can-head', 'can-host']);
        $data = $request->all();
        $data['status'] = 'new';
        $data['data'] = [
            'check' => $request->get('image'),
        ];

        $payment->create($data);
        return redirect()->route("{$this->root}.index")->with('success', 'Платеж успешно создан.');
    }

    /**
     * Display the specified resource.
     *
     * @param Payment $payment
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Payment $payment)
    {
        access(['can-operator', 'can-head', 'can-host']);

        return view("{$this->root}.show", [
            'payment' => $payment,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Payment $payment
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payment)
    {
        access(['can-operator', 'can-head', 'can-host']);
        return view("{$this->root}.edit", [
            'payment' => $payment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CreatePaymentRequest $request
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(CreatePaymentRequest $request, Payment $payment)
    {
        access(['can-operator', 'can-head', 'can-host']);
        $data = $request->all();
        $paymentData = $payment->data;
        if (isset($data['file'])) {
            $paymentData['check'] = $data['file'];
        }
        $paymentData['subscription']['from'] = $data['from'];
        $paymentData['subscription']['to'] = $data['to'];
        $payment->update([
            'quantity' => $data['quantity'],
            'amount' => $data['amount'],
            'data' => $paymentData,
        ]);

        $message = 'Данные платежа успешно изменены.';
        if ($request->ajax()) {
            return response()->json([
                'message' => $message,
            ]);
        } else {
            return redirect()->to(route("{$this->root}.show", [$payment->id]))->with('success', $message);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payment $payment
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment, Request $request)
    {
        access(['can-operator', 'can-head', 'can-host']);
        $payment->delete();
        $message = 'Платеж успешно удален.';
        if ($request->ajax()) {
            return response()->json([
                'message' => $message,
            ]);
        } else {
            return redirect()->route("{$this->root}.index")->with('success', $message);
        }
    }
}
