<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UsersBonusesController;
use App\Http\Controllers\StatisticsController;
use App\Models\Bonus;
use App\Models\Card;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StatisticsModel;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get("/test", function () {
    // $payments = Payment::whereStatus('Completed')->get()->groupBy('product_id');
    // foreach ($payments as $key => $productPayments) {
    //     $product = Product::whereId($key)->first();
    //     $productPaymentTypes = $product->paymentTypes->pluck('id', 'name')->toArray();
    //     $productBonuses = $product->productBonuses;
    //     unset($productPaymentTypes['tries']);
    //     foreach ($productPayments as $payment) {
    //         $countBeforePayments = $payment->subscription->payments
    //             ->where('status', 'Completed')
    //             ->where('type', $payment->type)
    //             ->where('paided_at', '<', $payment->paided_at)
    //             ->where('id', '!=', $payment->id)
    //             ->count();

    //         if ($countBeforePayments < 1) {
    //             $bonus = $productBonuses
    //                 ->where('type', 'firstPayment')
    //                 ->where('payment_type_id', $productPaymentTypes[$payment->type])->first();
    //             if (! isset($bonus)) {
    //                 dd($payment);
    //             }
    //             $payment->update([
    //                 'product_bonus_id' => $bonus->id,
    //             ]);
    //         } else {
    //             $bonus = $productBonuses
    //                 ->where('type', 'repeatedPayment')
    //                 ->where('payment_type_id', $productPaymentTypes[$payment->type])
    //                 ->first();

    //             if (! isset($bonus)) {
    //                 dd($payment);
    //             }
    //             $payment->update([
    //                 'product_bonus_id' => $bonus->id,
    //             ]);
    //         }
    //     }
    // }
})->name("test");

Route::get("/test2", function () {
    $payments = Payment::whereType('cloudpayments')->whereStatus('Completed')->get();
    foreach ($payments as $payment) {
        if (isset($payment->data['cloudpayments']) && isset($payment->data['cloudpayments']['Token']) && isset($payment->customer)) {
            Card::updateOrCreate([
                'customer_id' => $payment->customer->id,
                'token' => $payment->data['cloudpayments']['Token'],
            ],
            [
                'customer_id' => $payment->customer->id,
                'cp_account_id' => $payment->data['cloudpayments']['AccountId'] ?? null,
                'token' => $payment->data['cloudpayments']['Token'],
                'first_six' => $payment->data['cloudpayments']['CardFirstSix'] ?? null,
                'last_four' => $payment->data['cloudpayments']['CardLastFour'] ?? null,
                'exp_date' => $payment->data['cloudpayments']['CardExpDate'] ?? null,
                'type' => $payment->data['cloudpayments']['CardType'] ?? null,
                'name' => $payment->data['cloudpayments']['Name'] ?? '',
    
            ]);
        }
    }
})->name("test2");

Route::get("/test3", function () {
    $subscriptions = Subscription::
        // whereNull('team_id')
        // ->whereStatus('paid')
        whereNotIn('product_id', [1, 3])
        ->get();
        // ->groupBy('product_id');

    $teams = [
        'zhir-v-minus' => 1,
        'yoga-lates' => 2
    ];

    // foreach ($productsSubscriptions as $productId => $subscriptions) {
        foreach ($subscriptions as $subscription) {
            $subs = $subscription->customer->subscriptions->where('team_id', '!=', null)->where('product_id', '!=', $subscription->productId);
            $subscriptionCount = $subs->count();

            if ($subscriptionCount == 0) {
                $team = rand(1,2);
            } else if ($subscriptionCount == 1) {
                $team = $subs->first()->team_id;
            } else {
                $team = $subs->first()->team_id;
            }

            $subscription->update([
                'team_id' => $team,
                // 'team_id' => $teams[$subscription->product->code],
            ]);
        }
    // }
})->name("test3");

Route::get("/test4", function () {
    $payments = Payment::
        whereStatus('Completed')
        // ->whereNull('team_id')
        ->get();
    $paymentIds = [];
    foreach ($payments as $payment) {
        if (isset($payment->subscription) && isset($payment->subscription->product)) {
            $payment->update([
                'team_id' => $payment->subscription->team_id,
            ]);
        } else {
            $paymentIds[] = $payment->id;
            // dd($payment, $payment->subscription, $payment->product);
        }
    }

    dd($paymentIds);
})->name("test4");

Route::get("/", [HomeController::class, "homepage"])->name("homepage");
Route::get("/thank-you", [HomeController::class, "thankYou"])->name("thankYou");
Auth::routes();

Route::middleware(["auth", 'auth.user'])->group(function () {
    Route::get("/dashboard", [HomeController::class, "dashboard"])->name("dashboard");
    Route::post("/statistics", [StatisticsController::class, "update"])->name("statistics.update");
    Route::post("/statistics/timeline", [StatisticsController::class, "updateTimeline"])->name("statistics_timeline.update");
    Route::get("/statistics/quantitative", [StatisticsController::class, "quantitative"])->name("statistics.quantitative");
    Route::get("/statistics/financial", [StatisticsController::class, "financial"])->name("statistics.financial");
    Route::get("/search", 'SearchController@search')->name("search");

    Route::get("/users-bonuses", [UsersBonusesController::class, "show"])->name("users_bonuses.show");

    Route::post('customers/update-with-data', 'CustomerController@createWithData');
    Route::get('customers/list', 'CustomerController@getList');
    Route::get('customers/get-options', 'CustomerController@getOptions');
    Route::get('customers/{customerId}/with-data', 'CustomerController@getCustomerWithData');
    Route::get('customers/filter', 'CustomerController@getFilters');

    Route::get('products/list', 'ProductController@getList');
    Route::get('products/filter', 'ProductController@getFilters');

    Route::get('teams/list', 'TeamController@getList');
    Route::get('teams/filter', 'TeamController@getFilters');

    Route::get('users/list', 'UserController@getList');
    Route::get('users/filter', 'UserController@getFilters');

    Route::get('notifications/list', 'NotificationController@getList');
    Route::get('notifications/filter', 'NotificationController@getFilters');

    Route::get('subscriptions/list', 'SubscriptionController@getList');
    Route::get('subscriptions/filter', 'SubscriptionController@getFilters');
    Route::post('subscriptions/manualWriteOffPayment', 'SubscriptionController@manualWriteOffPayment');
    Route::post('subscriptions/writeOffPaymentByToken', 'SubscriptionController@writeOffPaymentByToken');

    Route::get('userlogs/list', 'UserLogController@getList');
    Route::get('userlogs/filter', 'UserLogController@getFilters');

    Route::get('payments/list', 'PaymentController@getList');
    Route::get('payments/filter', 'PaymentController@getFilters');

    Route::get('products/with-prices', 'ProductController@withPrices');

    Route::resources([
        'customers' => 'CustomerController',
        'products' => 'ProductController',
        'teams' => 'TeamController',
        'subscriptions' => 'SubscriptionController',
        'payments' => 'PaymentController',
        'users' => 'UserController',
        'notifications' => 'NotificationController',
        'userlogs' => 'UserLogController',
    ]);
});

Route::get("/pull", [HomeController::class, "pull"])->name("pull");
Route::get('cloudpayments/{subscriptionId}', 'CloudPaymentsController@showWidget')->name('cloudpayments.show_widget');
Route::get('cloudpayments/{subscriptionId}/thank-you', 'CloudPaymentsController@thankYou')->name('cloudpayments.thank_you');
