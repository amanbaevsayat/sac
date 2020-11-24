<?php

namespace App\Http\Controllers;

use App\Models\Payment;
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
            'publicId' => $publicId,
        ]);
    }
}
