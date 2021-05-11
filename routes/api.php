<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('upload-file', 'FileController@uploadFile');
// Route::post('cloudpayments/pay', 'CloudPaymentsController@pay')->name('cloudpayments.pay');
// Route::post('cloudpayments/generate-payment', 'CloudPaymentsController@generatePayment')->name('cloudpayments.generate_payment');
// Route::post('cloudpayments/post3ds', 'CloudPaymentsController@post3ds')->name('cloudpayments.post3ds');
// Route::post('cloudpayments/change-status', 'CloudPaymentsController@changeStatus')->name('cloudpayments.changeStatus');


Route::post('cloudpayments/check', 'CloudPaymentsController@checkNotification')->name('cloudpayments.notifications.check');
Route::post('cloudpayments/pay-fail', 'CloudPaymentsController@payFailNotification')->name('cloudpayments.notifications.pay');
Route::post('cloudpayments/confirm', 'CloudPaymentsController@confirmNotification')->name('cloudpayments.notifications.confirm');
Route::post('cloudpayments/refund', 'CloudPaymentsController@refundNotification')->name('cloudpayments.notifications.refund');
Route::post('cloudpayments/recurrent', 'CloudPaymentsController@recurrentNotification')->name('cloudpayments.notifications.recurrent');
Route::post('cloudpayments/cancel', 'CloudPaymentsController@cancelNotification')->name('cloudpayments.notifications.cancel');
