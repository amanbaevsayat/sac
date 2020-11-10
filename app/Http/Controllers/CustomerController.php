<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Remark;
use App\Filters\CustomerFilter;
use App\Http\Resources\CustomerCollection;

class CustomerController extends Controller
{
    private $root;
    private $perPage;

    public function __construct()
    {
        $this->root = 'customers';
        $this->perPage = 45;
    }

    public function getList(CustomerFilter $filters)
    {
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

        $query = Customer::query();
        $customers = $query->filter($filters)->paginate($this->perPage)->appends(request()->all());

        return response()->json(new CustomerCollection($customers), 200);
    }

    public function getFilters()
    {
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

        $remarks = Remark::pluck('title', 'id')->toArray();

        $data = [
            [
                'name' => 'name',
                'title' => 'Имя',
                'type' => 'input',
            ],
            [
                'name' => 'phone',
                'title' => 'Телефон',
                'type' => 'input',
            ],
            [
                'name' => 'email',
                'title' => 'E-mail',
                'type' => 'input',
            ],
            [
                'name' => 'remark_id',
                'title' => 'Метки',
                'type' => 'select',
                'options' => $remarks,
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

        $remarks = Remark::all();

        return view("{$this->root}.create", [
            'remarks' => $remarks,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateCustomerRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCustomerRequest $request, Customer $customer)
    {
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

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
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

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
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

        $remarks = Remark::all();

        return view("{$this->root}.edit", [
            'remarks' => $remarks,
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
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);
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
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

        $customer->delete();
        return redirect()->route("{$this->root}.index")->with('success', 'Клиент успешно удален.');
    }
}
