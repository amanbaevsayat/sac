<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Filters\NotificationFilter;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\NotificationCollection;
use App\Models\UserLog;
use App\Services\CloudPaymentsService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    private $root;
    private $perPage;

    public function __construct()
    {
        $this->root = 'notifications';
        $this->perPage = 45;
    }

    public function getList(NotificationFilter $filters)
    {
        access(['can-operator', 'can-head', 'can-host']);
        $request = request()->all();
        
        $query = Notification::query();
        $notifications = $query->filter($filters)->paginate($this->perPage)->appends($request);

        return response()->json((new NotificationCollection($notifications)), 200);
    }

    public function getFilters()
    {
        access(['can-operator', 'can-head', 'can-host']);
        $products = Product::get()->pluck('title', 'id');

        $data['main'] = [
            [
                'name' => 'product_id',
                'title' => 'Услуги',
                'type' => 'select-multiple',
                'options' => $products,
            ],
            [
                'name' => 'processed',
                'title' => 'Тип задач',
                'type' => 'select-multiple',
                'options' => [
                    '0' => 'Задачи',
                    '1' => 'Архив задач'
                ],
            ],
            [
                'name' => 'type',
                'title' => 'Тип уведомления',
                'type' => 'select-multiple',
                'options' => Notification::TYPES,
            ],
        ];

        // $data['second'] = [
        //     // [
        //     //     'name' => 'customer_name_or_phone',
        //     //     'placeholder' => 'Найти по имени и номеру',
        //     //     'title' => 'Клиенты',
        //     //     'type' => 'input-search',
        //     //     'key' => 'customer',
        //     //     'options' => [],
        //     // ],
        // ];

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
    // public function store(CreateCustomerRequest $request, Customer $customer)
    // {
    //     access(['can-operator', 'can-head', 'can-host']);

    //     $customer->create($request->all());
    //     return redirect()->route("{$this->root}.index")->with('success', 'Клиент успешно создан.');
    // }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function show(Notification $notification)
    {
        access(['can-operator', 'can-head', 'can-host']);

        return view("{$this->root}.show", [
            'notification' => $notification,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function edit(Notification $notification)
    {
        access(['can-operator', 'can-head', 'can-host']);

        return view("{$this->root}.edit", [
            'notification' => $notification,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  \App\Models\Notification $notification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notification $notification)
    {
        access(['can-operator', 'can-head', 'can-host']);
        $data = [
            'user_id' => Auth::id(),
            'in_process' => $request->get('in_process') ?? $notification->in_process,
            'processed' => $request->get('processed') ?? $notification->processed,
        ];

        if (isset($notification->subscription)) {
            $subscription = $notification->subscription;
            
            $notification->subscription->update([
                'status' => $request->get('status'),
            ]);
        }

        $notification->update($data);

        $message = 'Уведомление успешно изменено.';
        if ($request->ajax()) {
            return response()->json([
                'message' => $message,
            ]);
        } else {
            return redirect()->to(route("{$this->root}.show", [$notification->id]))->with('success', $message);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Notification $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification)
    {
        access(['can-operator', 'can-head', 'can-host']);

        $notification->delete();
        return redirect()->route("{$this->root}.index")->with('success', 'Уведомление успешно удален.');
    }
}
