<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Filters\ProductFilter;
use App\Http\Resources\PaymentTypeResource;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductUsersResource;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Price;
use App\Models\ProductBonus;
use App\Models\Reason;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;

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
        access(['can-head', 'can-host']);

        $query = Product::query();
        $products = $query->latest()->filter($filters)->paginate($this->perPage)->appends(request()->all());

        return response()->json(new ProductCollection($products), 200);
    }

    public function getFilters()
    {
        access(['can-head', 'can-host']);

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
        access(['can-head', 'can-host']);

        return view("{$this->root}.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        access(['can-head', 'can-host']);
        $paymentTypes = PaymentType::whereIsActive(true)->get()->pluck('title', 'name')->toArray();
        $users = User::all()->pluck('account', 'id')->toArray();

        return view("{$this->root}.create", [
            'paymentTypes' => $paymentTypes,
            'users' => $users,
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
        access(['can-head', 'can-host']);
        $this->updateOrCreate($request->all(), $product, 'create');

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
        access(['can-head', 'can-host']);

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
        access(['can-head', 'can-host']);
        $productPrices = $product->prices()->pluck('price');
        $productUsers = $product->users;
        $reasons = $product->reasons()->where('is_active', true)->pluck('title');
        $users = User::all()->pluck('account', 'id')->toArray();
        $productPaymentTypes = $product->paymentTypes;
        $paymentTypes = PaymentType::whereIsActive(true)->get()->pluck('title', 'name')->toArray();

        return view("{$this->root}.edit", [
            'product' => $product,
            'productPrices' => $productPrices,
            'reasons' => $reasons,
            'productPaymentTypes' => PaymentTypeResource::collection($productPaymentTypes),
            'paymentTypes' => $paymentTypes,
            'productUsers' => ProductUsersResource::collection($productUsers),
            'users' => $users,
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
        access(['can-head', 'can-host']);
        $this->updateOrCreate($request->all(), $product, 'update');

        $message = 'Данные продукта успешно изменены.';
        if ($request->ajax()) {
            return response()->json([
                'message' => $message,
            ]);
        } else {
            return redirect()->to(route("{$this->root}.show", [$product->id]))->with('success', $message);
        }
    }

    private function updateOrCreate(array $request, ?Product $product, $type) 
    {
        $priceIds = [];
        $reasonIds = [];
        $paymentTypeIds = [];
        $prices = $request['prices'] ?? [];
        $productUsers = $request['productUsers'] ?? [];
        $paymentTypes = $request['paymentTypes'] ?? [];
        $productReasons = $request['reasons'] ?? [];

        if ($type == 'update') {
            $product->update($request);
        } else if ($type == 'create') {
            $product = $product->create($request);
        }

        foreach ($prices as $item) {
            if ($item) {
                $price = Price::updateOrCreate([
                    'price' => $item,
                    'product_id' => $product->id,
                ]);
                $priceIds[] = $price->id;
            }
        }

        foreach ($productReasons as $reason) {
            $productReason = Reason::updateOrCreate([
                'title' => $reason,
                'product_id' => $product->id,
            ], [
                'is_active',
                'title' => $reason,
                'product_id' => $product->id,
            ]);
            $reasonIds[] = $productReason->id;
        }

        $product->paymentTypes()->detach();
        foreach ($paymentTypes as $item) {
            $paymentType = PaymentType::whereName($item['type'])->firstOrFail();
            $product->paymentTypes()->attach([$paymentType->id]);
            $bonusIds = [];
            if (isset($item['bonuses'])) {
                foreach ($item['bonuses'] as $type => $amount) {
                    $bonus = ProductBonus::updateOrCreate([
                        'product_id' => $product->id,
                        'payment_type_id' => $paymentType->id,
                        'type' => $type,
                        'amount' => $amount,
                    ], [
                        'product_id' => $product->id,
                        'payment_type_id' => $paymentType->id,
                        'type' => $type,
                        'is_active' => true,
                        'amount' => $amount,
                    ]);

                    $bonusIds[] = $bonus->id;
                }

                $paymentType->productBonuses()->whereNotIn('id', $bonusIds)->update([
                    'is_active' => false,
                ]);
            }
        }

        $product->prices()->whereNotIn('id', $priceIds)->delete();
        $product->reasons()->whereNotIn('id', $reasonIds)->update([
            'is_active' => false,
        ]);
        $product->users()->detach();

        foreach ($productUsers as $productUser) {
            $product->users()->attach([
                $productUser['id'] => [
                    'stake' => $productUser['stake'],
                    'employment_at' => Carbon::parse($productUser['employment_at']),
                ],
            ]);
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
        access(['can-head', 'can-host']);

        $product->delete();
        return redirect()->route("{$this->root}.index")->with('success', 'Продукт успешно удален.');
    }

    public function withPrices()
    {
        access(['can-head', 'can-host', 'can-operator']);

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
