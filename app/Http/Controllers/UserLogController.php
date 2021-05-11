<?php

namespace App\Http\Controllers;

use App\Filters\UserLogFilter;
use App\Models\Payment;
use App\Models\Product;
use App\Models\UserLog;
use App\Models\StatisticsModel;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\UserLogCollection;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserLogController extends Controller
{
    private $root;
    private $perPage;

    public function __construct()
    {
        $this->root = 'userlogs';
        $this->perPage = 45;
    }

    public function getList(UserLogFilter $filters)
    {
        access(['can-operator', 'can-head', 'can-host']);

        $query = UserLog::query();
        $userlogs = $query->filter($filters)->latest()->paginate($this->perPage)->appends(request()->all());

        return response()->json(new UserLogCollection($userlogs), 200);
    }

    public function getFilters()
    {
        access(['can-operator', 'can-head', 'can-host']);
        $users = User::get()->pluck('account', 'id');
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
                'title' => 'Тип лога',
                'type' => 'select-multiple',
                'options' => UserLog::TYPES,
            ],
            [
                'name' => 'user_id',
                'title' => 'Операторы',
                'type' => 'select-multiple',
                'options' => $users,
            ],
            [
                'name' => 'subscription_id',
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
     * Display the specified resource.
     *
     * @param UserLog $userlog
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, UserLog $userlog)
    {
        access(['can-operator', 'can-head', 'can-host']);

        return view("{$this->root}.show", [
            'userlog' => $userlog,
        ]);
    }
}
