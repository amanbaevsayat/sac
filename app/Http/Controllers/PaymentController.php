<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePaymentRequest;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Filters\PaymentFilter;
use App\Http\Resources\PaymentCollection;
use App\Models\Customer;
use App\Models\Subscription;
use Illuminate\Support\Str;
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
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

        $query = Payment::query();
        $payments = $query->filter($filters)->paginate($this->perPage)->appends(request()->all());

        return response()->json(new PaymentCollection($payments), 200);
    }

    public function getFilters()
    {
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);
        $subscriptions = Subscription::get()->pluck('description', 'id');
        $customers = Customer::get()->pluck('name_with_phone', 'id');
        
        $data = [
            [
                'name' => 'customer_id',
                'title' => 'Клиенты',
                'type' => 'select',
                'options' => $customers,
            ],
            [
                'name' => 'subscription_id',
                'title' => 'Подписки',
                'type' => 'select',
                'options' => $subscriptions,
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
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);
        $data = $request->all();
        $data['slug'] = Str::uuid();
        $data['recurrent'] = $request->get('recurrent') == 'on' ? true : false;
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
    public function show(Request $request, $payment)
    {
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

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
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

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
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);
        $payment->update($request->all());

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
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

        $payment->delete();
        return redirect()->route("{$this->root}.index")->with('success', 'Платеж успешно удален.');
    }
}
