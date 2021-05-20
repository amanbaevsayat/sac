<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CloudPaymentsController extends Controller
{
    public function showWidget(int $subscriptionId, Request $request)
    {
        $subscription = Subscription::whereId($subscriptionId)->whereNull('cp_subscription_id')->where('status', '!=', 'paid')->firstOr(function () {
            abort(404);
        });

        $payment = $subscription->payments()->whereStatus('new')->whereType('cloudpayments')->first();

        // Если у юзера вышла ошибка с платежом, создаем новый
        if (!$payment) {
            $lastPayment = $subscription->payments()->latest()->whereNotNull('user_id')->whereType('cloudpayments')->first();
            $payment = $subscription->payments()->create([
                'customer_id' => $subscription->customer->id,
                'product_id' => $subscription->product->id,
                'user_id' => $lastPayment->user_id ?? null,
                'type' => 'cloudpayments',
                'status' => 'new',
                'amount' => $subscription->price,
                'quantity' => 1,
                'paided_at' => Carbon::now(),
            ]);
        }
        // $payment->amount = 10;
        $product = $subscription->product;
        $customer = $subscription->customer;

        $data = [
            'cloudPayments' => [
                'recurrent' => [
                    'interval' => 'Month',
                    'period' => 1,
                    'customerReceipt' => [
                        'Items' => [ //товарные позиции
                            [
                                'label' => $product->title, // наименование товара
                                'price' => $payment->amount, // цена
                                'quantity' => 1.00, //количество
                                'amount' => $payment->amount, // сумма
                                'vat' => 0, // ставка НДС
                                'method' => 0, // тег-1214 признак способа расчета - признак способа расчета
                                'object' => 0, // тег-1212 признак предмета расчета - признак предмета товара, работы, услуги, платежа, выплаты, иного предмета расчета
                                'measurementUnit' => "шт" //единица измерения
                            ],
                        ],
                        'taxationSystem' => 0, //система налогообложения; необязательный, если у вас одна система налогообложения
                        'email' => $customer->email, //e-mail покупателя, если нужно отправить письмо с чеком
                        'phone' => $customer->phone, //телефон покупателя в любом формате, если нужно отправить сообщение со ссылкой на чек
                        'isBso' => false,
                    ],
                ],
                'customerReceipt' => [
                    'Items' => [ //товарные позиции
                        [
                            'label' => $product->title, // наименование товара
                            'price' => $payment->amount, // цена
                            'quantity' => 1.00, //количество
                            'amount' => $payment->amount, // сумма
                            'vat' => 0, // ставка НДС
                            'method' => 0, // тег-1214 признак способа расчета - признак способа расчета
                            'object' => 0, // тег-1212 признак предмета расчета - признак предмета товара, работы, услуги, платежа, выплаты, иного предмета расчета
                            'measurementUnit' => "шт" //единица измерения
                        ],
                    ],
                    'calculationPlace' => "www.strela-academy.ru", //место осуществления расчёта, по умолчанию берется значение из кассы
                    'taxationSystem' => 0, //система налогообложения; необязательный, если у вас одна система налогообложения
                    'email' => $customer->email, //e-mail покупателя, если нужно отправить письмо с чеком
                    'phone' => $customer->phone, //телефон покупателя в любом формате, если нужно отправить сообщение со ссылкой на чек
                    'isBso' => false,
                ],
            ],
            'product' => [
                'id' => $product->id,
            ],
            'subscription' => [
                'id' => $subscription->id,
            ],
        ];

        $publicId = env('CLOUDPAYMENTS_USERNAME');

        return view('cloudpayments.show-widget', [
            'payment' => $payment,
            'customer' => $subscription->customer,
            'subscription' => $subscription,
            'product' => $subscription->product,
            'price' => $subscription->price,
            'data' => json_encode($data),
            'publicId' => $publicId,
        ]);
    }

    public function thankYou(int $subscriptionId, Request $request)
    {
        $subscription = Subscription::whereId($subscriptionId)->firstOr(function () {
            abort(404);
        });

        return view('cloudpayments.thank-you', [
            'subscription' => $subscription,
        ]);
    }

    // public function showCheckout(int $subscriptionId, Request $request)
    // {
    //     $subscription = Subscription::whereId($subscriptionId)->where('status', '!=', 'paid')->firstOr(function () {
    //         abort(404);
    //     });

    //     $payment = $subscription->payments()->whereStatus('new')->whereType('cloudpayments')->first();

    //     // Если у юзера вышла ошибка с платежом, создаем новый
    //     if (!isset($payment)) {
    //         $payment = $subscription->payments()->create([
    //             'customer_id' => $subscription->customer->id,
    //             'user_id' => null,
    //             'quantity' => 1,
    //             'type' => 'cloudpayments',
    //             'status' => 'new',
    //             'amount' => $subscription->price,
    //         ]);
    //     } else {
    //         $payment->update([
    //             'amount' => $subscription->price,
    //         ]);
    //     }

    //     $publicId = env('CLOUDPAYMENTS_USERNAME');

    //     return view('cloudpayments.show-checkout', [
    //         'payment' => $payment,
    //         'customer' => $subscription->customer,
    //         'subscription' => $subscription,
    //         'product' => $subscription->product,
    //         'publicId' => $publicId,
    //     ]);
    // }
}
