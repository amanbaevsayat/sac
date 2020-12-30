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
Route::post('cloudpayments/pay', 'CloudPaymentsController@pay')->name('cloudpayments.pay');
Route::post('cloudpayments/post3ds', 'CloudPaymentsController@post3ds')->name('cloudpayments.post3ds');
