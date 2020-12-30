<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Filters\ProductFilter;
use App\Http\Resources\ProductCollection;
use App\Models\Price;

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
        access(['can-owner', 'can-host']);

        $query = Product::query();
        $products = $query->latest()->filter($filters)->paginate($this->perPage)->appends(request()->all());

        return response()->json(new ProductCollection($products), 200);
    }

    public function getFilters()
    {
        access(['can-owner', 'can-host']);

        $data['main'] = [
            [
                'name' => 'title',
                'title' => 'Заголовок',
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
        access(['can-owner', 'can-host']);

        return view("{$this->root}.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        access(['can-owner', 'can-host']);

        return view("{$this->root}.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateProductRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProductRequest $request, Product $product)
    {
        access(['can-owner', 'can-host']);
        $product = $product->create($request->all());
        $priceIds = [];
        $prices = $request->get('prices', []);

        foreach ($prices as $item) {
            if ($item) {
                $price = Price::updateOrCreate([
                    'price' => $item,
                    'product_id' => $product->id,
                ]);
                $priceIds[] = $price->id;
            }
        }
        $product->prices()->whereNotIn('id', $priceIds)->delete();
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
        access(['can-owner', 'can-host']);

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
        access(['can-owner', 'can-host']);
        $productPrices = $product->prices()->pluck('price');
        return view("{$this->root}.edit", [
            'product' => $product,
            'productPrices' => $productPrices,
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
        access(['can-owner', 'can-host']);
        $priceIds = [];
        $prices = $request->get('prices', []);
        $product->update($request->all());

        foreach ($prices as $item) {
            if ($item) {
                $price = Price::updateOrCreate([
                    'price' => $item,
                    'product_id' => $product->id,
                ]);
                $priceIds[] = $price->id;
            }
        }
        $product->prices()->whereNotIn('id', $priceIds)->delete();

        $message = 'Данные продукта успешно изменены.';
        if ($request->ajax()) {
            return response()->json([
                'message' => $message,
            ]);
        } else {
            return redirect()->to(route("{$this->root}.show", [$product->id]))->with('success', $message);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        access(['can-owner', 'can-host']);

        $product->delete();
        return redirect()->route("{$this->root}.index")->with('success', 'Продукт успешно удален.');
    }

    public function withPrices()
    {
        access(['can-owner', 'can-host', 'can-operator']);

        $products = Product::get();
        $data = [];
        foreach ($products as $product) {
            $data[$product->id] = [
                'title' => $product->title,
                'prices' => [],
            ];
            if (count($product->prices) > 0) {
                $prices = [];
                foreach ($product->prices as $price) {
                    $prices[$price->id] = $price->price;
                }
                $data[$product->id]['prices'] = $prices;
            }
        }

        return response()->json($data, 200);
    }
}
