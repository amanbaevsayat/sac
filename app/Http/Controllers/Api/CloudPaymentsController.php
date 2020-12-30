<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ErrorCodes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostThreeDSRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Payment;
use App\Services\CloudPaymentsService;
use Illuminate\Support\Facades\View;

class CloudPaymentsController extends Controller
{
    public function pay(Request $request)
    {
        $packet = $request->get('packet');
        $slug = $request->get('slug');
        $cardName = $request->get('cardName');

        $payment = Payment::whereSlug($slug)->first();

        if (!$payment) {
            throw new \Exception('Платеж не найден.', 500);
        }

        $json_data = array(
            'cloudPayments' => array(
                'recurrent' => array(
                    'Interval' => 'Month',
                    'Period' => 1,
                    'Amount' => $payment->amount,
                    // 'Amount' => 10,
                )
            )
        );

        $data = [
            'Currency' => 'KZT',
            // "Amount" => 10,
            "Amount" => $payment->amount,
            "InvoiceId" => $payment->id,
            "Description" => '',
            "AccountId" => $payment->customer->phone,
            "Name" => $cardName,
            "CardCryptogramPacket" => $packet,
            'JsonData' => json_encode($json_data),
        ];

        $cloudPaymentsService = new CloudPaymentsService();
        $response = $cloudPaymentsService->paymentsCardsAuth($data);
        if (isset($response['Model']['AcsUrl'])) {
            $response['Model']['TermUrl'] = route('cloudpayments.post3ds');
            $response['acs_form'] = view('cloudpayments.3ds-form', $response['Model'])->render();
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
}
