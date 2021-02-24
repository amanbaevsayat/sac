<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CloudPaymentsController extends Controller
{
    public function showCheckout(int $subscriptionId, Request $request)
    {
        $subscription = Subscription::whereId($subscriptionId)->where('status', '!=', 'paid')->firstOr(function () {
            abort(404);
        });

        $payment = $subscription->payments()->whereStatus('new')->whereType('cloudpayments')->first();

        // Если у юзера вышла ошибка с платежом, создаем новый
        if (!isset($payment)) {
            $payment = $subscription->payments()->create([
                'customer_id' => $subscription->customer->id,
                'user_id' => null,
                'quantity' => 1,
                'type' => 'cloudpayments',
                'status' => 'new',
                'amount' => $subscription->price,
            ]);
        } else {
            $payment->update([
                'amount' => $subscription->price,
            ]);
        }

        $publicId = env('CLOUDPAYMENTS_USERNAME');

        return view('cloudpayments.show-checkout', [
            'payment' => $payment,
            'customer' => $subscription->customer,
            'subscription' => $subscription,
            'product' => $subscription->product,
            'price' => $subscription->price,
            'publicId' => $publicId,
        ]);
    }

    public function showWidget(int $subscriptionId, Request $request)
    {
        $subscription = Subscription::whereId($subscriptionId)->where('status', '!=', 'paid')->firstOr(function () {
            abort(404);
        });

        $payment = $subscription->payments()->whereStatus('new')->whereType('cloudpayments')->first();

        // Если у юзера вышла ошибка с платежом, создаем новый
        if (!$payment) {
            $lastPayment = $subscription->payments()->latest()->whereNotNull('user_id')->whereType('cloudpayments')->first();
            $payment = $subscription->payments()->create([
                'customer_id' => $subscription->customer->id,
                'user_id' => $lastPayment->user_id ?? null,
                'type' => 'cloudpayments',
                'status' => 'new',
                'amount' => $subscription->price,
                'quantity' => 1,
                'paided_at' => Carbon::now(),
            ]);
        }

        $publicId = env('CLOUDPAYMENTS_USERNAME');

        return view('cloudpayments.show-widget', [
            'payment' => $payment,
            'customer' => $subscription->customer,
            'subscription' => $subscription,
            'product' => $subscription->product,
            'price' => $subscription->price,
            'publicId' => $publicId,
        ]);
    }
}
