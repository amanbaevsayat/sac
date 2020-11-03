<?php

namespace App\Http\Controllers\Api;

use App\Filters\CustomerFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerCollection;
use App\Exceptions\ErrorCodes;
use App\Models\Customer;
use App\Models\Remark;
use App\Http\Requests\CustomerUpdateApiRequest;

class CustomerController extends Controller
{
    private $perPage;

    public function __construct()
    {
        $this->perPage = 45;
    }

    public function index(CustomerFilter $filters)
    {
        $query = Customer::query();
        $customers = $query->filter($filters)->paginate($this->perPage)->appends(request()->all());

        return response()->json(new CustomerCollection($customers), 200);
    }

    public function getFilters()
    {
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

    public function update(int $id, CustomerUpdateApiRequest $request)
    {
        $customer = Customer::whereId($id)->firstOr(function () use ($id) {
            throw new \Exception('Клиент '.$id.' не найден ', ErrorCodes::NOT_FOUND);
        });
        $customer->update($request->all());
        return response()->json([
            'message' => 'Клиент успешно обновлен',
        ], 200);
    }
}
