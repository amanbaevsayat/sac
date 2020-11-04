<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Remark;
use App\Filters\ProductFilter;
use App\Http\Resources\ProductCollection;

class ProductController extends Controller
{
    private $root;
    private $perPage;

    public function __construct()
    {
        $this->root = 'products';
        $this->perPage = 45;
    }

    public function getList(ProductFilter $filters)
    {
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

        $query = Product::query();
        $products = $query->filter($filters)->paginate($this->perPage)->appends(request()->all());

        return response()->json(new ProductCollection($products), 200);
    }

    public function getFilters()
    {
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

        $remarks = Remark::pluck('title', 'id')->toArray();

        $data = [
            [
                'name' => 'code',
                'title' => 'Код',
                'type' => 'input',
            ],
            [
                'name' => 'title',
                'title' => 'Заголовок',
                'type' => 'input',
            ],
            [
                'name' => 'description',
                'title' => 'Описание',
                'type' => 'input',
            ],
            [
                'name' => 'price',
                'title' => 'Цена',
                'type' => 'input',
            ],
            [
                'name' => 'trial_price',
                'title' => 'Пробная цена',
                'type' => 'input',
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
     * @param  CreateProductRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProductRequest $request, Product $product)
    {
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

        $product->create($request->all());
        return redirect()->route("{$this->root}.index")->with('success', 'Продукт успешно создан.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

        return view("{$this->root}.show", [
            'product' => $product,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

        $remarks = Remark::all();

        return view("{$this->root}.edit", [
            'remarks' => $remarks,
            'product' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CreateProductRequest $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(CreateProductRequest $request, Product $product)
    {
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

        $product->update($request->all());
        return redirect()->to(route("{$this->root}.show", [$product->id]))->with('success', 'Данные продукта успешно изменены.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        access(['can-operator', 'can-manager', 'can-owner', 'can-host']);

        $product->delete();
        return redirect()->route("{$this->root}.index")->with('success', 'Продукт успешно удален.');
    }
}
