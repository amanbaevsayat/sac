<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ErrorCodes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostThreeDSRequest;
use App\Models\Customer;
use Illuminate\Support\Facades\Storage;
use App\Models\Payment;
use App\Services\CloudPaymentsService;
use Illuminate\Support\Facades\View;

class CloudPaymentsController extends Controller
{
    public function pay(Request $request)
    {
        $packet = $request->get('packet');
        $slug = $request->get('id');
        $cardName = $request->get('cardName');

        $payment = Payment::whereSlug($slug)->first();

        if (!isset($payment)) {
            throw new \Exception('Платеж не найден.', 500);
        }

        $json_data = array(
            'cloudPayments' => array(
                'recurrent' => array(
                    'Interval' => 'Month',
                    'Period' => 1,
                    'Amount' => $payment->subscription->price ?? $payment->amount,
                    'SubscriptionId' => $payment->subscription->id,
                    // 'Amount' => 10,
                )
            )
        );

        $data = [
            'Currency' => 'KZT',
            // "Amount" => 10,
            "Amount" => $payment->subscription->price ?? $payment->amount,
            "InvoiceId" => $payment->id,
            "Description" => '',
            "AccountId" => $payment->customer->phone,
            "Email" => $request->get('email', $payment->customer->email),
            "Name" => $cardName,
            "CardCryptogramPacket" => $packet,
            'JsonData' => json_encode($json_data),
        ];

        $cloudPaymentsService = new CloudPaymentsService();
        $response = $cloudPaymentsService->paymentsCardsAuth($data);
        if (isset($response['Model']['AcsUrl']) && $response['Success'] === false) {
            $response['Model']['TermUrl'] = route('cloudpayments.post3ds');
            $response['acs_form'] = view('cloudpayments.3ds-form', $response['Model'])->render();
        } elseif ($response['Success'] === false && isset($response['Model']['StatusCode']) && $response['Model']['StatusCode'] == 5) {
            $data = $payment->data ?? [];
            $data['cloudpayments'] = $response['Model'];
            $payment->update([
                'status' => $response['Model']['Status'],
                'data' => $data,
            ]);
            $response['acs_form'] = view('page.failure', [
                'message' => $response['Model']['CardHolderMessage'],
            ])->render();
        }

        return response()->json($response, 200);
    }

    public function post3ds(PostThreeDSRequest $request)
    {
        $data = [
            "TransactionId" => $request->get('MD'),
            "PaRes" => $request->get('PaRes'),
        ];
        $cloudPaymentsService = new CloudPaymentsService();
        $response = $cloudPaymentsService->paymentsCardsPost3ds($data);
        if ($response['Success']) {
            return view('page.success');
        } else {
            $payment = Payment::whereId($response['Model']['InvoiceId'])->first();
            if ($payment) {
                $data = $payment->data ?? [];
                $data['cloudpayments'] = $response['Model'];
                $payment->update([
                    'transaction_id' => $response['Model']['TransactionId'],
                    'status' => $response['Model']['Status'],
                    'data' => $data,
                ]);
            }
            if ($response['Model']['CardHolderMessage']) {
                return view('page.failure', [
                    'message' => $response['Model']['CardHolderMessage']
                ]);
            } else {
                return view('page.failure', [
                    'message' => $response['Message']
                ]);
            }
        }
    }

    public function changeStatus(Request $request)
    {
        $phone = $request->get('phone');
        $productId = $request->get('productId');

        $customer = Customer::where('phone', $phone)->firstOr(function () {
            abort(404);
        });

        $subscription = $customer->subscriptions()->where('product_id', $productId)->firstOr(function () {
            abort(404);
        });

        $subscription->update([
            'status' => 'paid'
        ]);

        return response()->json([
            'message' => 'Успешно обновлен',
        ], 200);
    }
}
