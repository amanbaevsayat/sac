<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\CloudPaymentsService;
use Illuminate\Http\Request;

class CloudPaymentsController extends Controller
{
    public function showWidget(string $slug, Request $request)
    {
        $payment = Payment::whereSlug($slug)->whereStatus('new')->whereType('cloudpayments')->firstOr(function () {
            abort(404);
        });

        $publicId = env('CLOUDPAYMENTS_USERNAME');

        return view('cloudpayments.show-widget', [
            'payment' => $payment,
            'customer' => $payment->customer,
            'subscription' => $payment->subscription,
            'product' => $payment->subscription->product,
            'price' => $payment->subscription->price,
            'publicId' => $publicId,
        ]);
    }

    public function pay(Request $request)
    {
        $packet = $request->get('packet');
        $slug = $request->get('slug');
        $cardName = $request->get('cardName');

        $payment = Payment::whereSlug($slug)->first();

        if (!$payment) {
            throw new \Exception('Платеж не найден.', 500);
        }
        $data = [
            "Amount" => $payment->amount * $payment->quantity,
            "InvoiceId" => $payment->id,
            "Description" => '',
            "AccountId" => $payment->customer->phone,
            "Name" => $cardName,
            "CardCryptogramPacket" => $packet,
        ];

        $cloudPaymentsService = new CloudPaymentsService();
        $response = $cloudPaymentsService->paymentsCardsCharge($data);
        return response()->json($response, 200);
    }

    public function AcsUrl(Request $request)
    {
        $paReq = $request->get('PaReq');
        $MD = $request->get('MD');
        $termUrl = $request->get('TermUrl');

        $data = [
            "TransactionId" => $MD,
            "PaRes" => $paReq,
        ];

        $cloudPaymentsService = new CloudPaymentsService();
        $response = $cloudPaymentsService->paymentsCardsPost3ds($data);
        return response()->json($response, 200);
    }
}
