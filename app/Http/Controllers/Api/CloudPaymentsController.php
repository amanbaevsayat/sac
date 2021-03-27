<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ErrorCodes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostThreeDSRequest;
use App\Models\Customer;
use App\Models\Notification;
use Illuminate\Support\Facades\Storage;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Subscription;
use App\Services\CloudPaymentsService;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Builder;
use App\Services\SubscriptionService;
use Carbon\Carbon;

class CloudPaymentsController extends Controller
{
    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

    /**
     * OrderController constructor.
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function recurrentNotification(Request $request)
    {
        \Log::info('recurrent');
        \Log::info($request->all());
        $data = $request->all();

        if ($data['Status'] == 'Cancelled') {
            $subscription = Subscription::where('cp_subscription_id', $data['Id'])->whereHas('customer', function (Builder $query) use ($data) {
                $query->where('phone', $data['AccountId']);
            })->first();
            if (! isset($subscription)) {
                \Log::error('Абонемент не найден. Phone: ' . $data['AccountId'] . '. SubscriptionId: ' . $data['Id']);
                return response()->json([
                    'code' => 0
                ]);
            }
            $subscription->update([
                'status' => 'refused'
            ]);
            $notification = Notification::create([
                'type' => Notification::TYPE_CANCEL_SUBSCRIPTION,
                'subscription_id' => $subscription->id,
                'product_id' => $subscription->product->id,
                'data' => [],
            ]);
            if (! isset($notification)) {
                \Log::error('Уведомление не создано. Phone: ' . $data['AccountId'] . '. SubscriptionId: ' . $data['Id']);
            }
        }

        return response()->json([
            'code' => 0
        ]);
    }

    public function checkNotification(Request $request)
    {
        \Log::info('check');
        \Log::info($request->all());
        return response()->json([
            'code' => 0,
        ]);
    }

    public function payFailNotification(Request $request)
    {
        \Log::info('pay-and-fail');
        $data = $request->all();
        \Log::info($data);
        $subscription = Subscription::where('cp_subscription_id', $data['SubscriptionId'])->whereNotNull('cp_subscription_id')->first();
        $customer = Customer::wherePhone($data['AccountId'])->first();

        if (! isset($customer)) {
            \Log::error('Не найден клиент. Transaction Id: ' . $data['TransactionId']);
            return response()->json([
                'code' => 0,
            ]);
        }
        $jsonData = '';
        if (isset($data['Data']) && is_string($data['Data'])) {
            $jsonData = json_decode($data['Data']);
        }
        
        if (! isset($subscription)) {
            if (isset($jsonData->subscription) && isset($jsonData->subscription->id) && json_last_error() == JSON_ERROR_NONE) {
                $subscription = Subscription::whereId($jsonData->subscription->id)->first();
            }
        }

        if (! isset($subscription)) {
            \Log::error('Не найден абонемент. Transaction Id: ' . $data['TransactionId']);
            return response()->json([
                'code' => 0,
            ]);
        }

        $subscriptionData = [
            'from' => null,
            'to' => null,
        ];
        if ($data['Status'] == 'Completed' && $data['SubscriptionId']) {
            $subscriptionData = [
                'from' => Carbon::createFromFormat('Y-m-d H:i:s', $subscription->ended_at, 'Asia/Almaty'),
                'to' => Carbon::createFromFormat('Y-m-d H:i:s', $subscription->ended_at, 'Asia/Almaty')->addMonths(1),
            ];
        }

        $data['CardHolderMessage'] = Payment::ERROR_CODES[$data['ReasonCode'] ?? 0];

        $payment = Payment::updateOrCreate([
            'transaction_id' => $data['TransactionId'],
        ], [
            'subscription_id' => $subscription->id,
            'customer_id' => $customer->id,
            'quantity' => 1,
            'type' => $subscription->payment_type,
            'status' => $data['Status'],
            'amount' => $data['Amount'],
            'paided_at' => Carbon::createFromFormat('Y-m-d H:i:s', $data['DateTime'], 'UTC')->setTimezone('Asia/Almaty'),
            'data' => [
                'cloudpayments' => $data,
                'subscription' => $subscriptionData,
            ],
        ]);

        if ($data['Status'] == 'Completed' && $data['SubscriptionId']) {
            $subscription->update([
                'cp_subscription_id' => $data['SubscriptionId'],
                'status' => 'paid',
                'ended_at' => Carbon::createFromFormat('Y-m-d H:i:s', $subscription->ended_at, 'Asia/Almaty')->addMonths(1),
            ]);
        }

        if (! isset($payment)) {
            \Log::error('Не создался платеж. Transaction Id: ' . $data['TransactionId']);
        }

        return response()->json([
            'code' => 0,
        ]);
    }

    public function confirmNotification(Request $request)
    {
        \Log::info('confirm');
        \Log::info($request->all());
        return response()->json([
            'code' => 0,
        ]);
    }

    public function refundNotification(Request $request)
    {
        \Log::info('refund');
        \Log::info($request->all());
        return response()->json([
            'code' => 0,
        ]);
    }

    public function cancelNotification(Request $request)
    {
        \Log::info('cancel');
        \Log::info($request->all());
        return response()->json([
            'code' => 0,
        ]);
    }

}
