<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\NoticeException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\UserLog;
use Illuminate\Database\Eloquent\Builder;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CloudPaymentsController extends Controller
{
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
        try {
            $data = $request->all();

            if ($data['Status'] == 'Cancelled') {
                $subscription = Subscription::where('cp_subscription_id', $data['Id'])
                ->first();
                if (! isset($subscription)) {
                    $subscription = Subscription::whereId($data['AccountId'])
                    ->first();
                    if (! isset($subscription)) {
                        $subscription = Subscription::whereHas('customer', function (Builder $query) use ($data) {
                            $query->where('phone', $data['AccountId']);
                        })
                        ->first();
                        if (! isset($subscription)) {
                            throw new NoticeException('Абонемент не найден. Phone: ' . $data['AccountId'] . '. SubscriptionId: ' . $data['Id']);
                        }
                    }
                }
                if ($subscription->status != 'refused') {
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
                $subscription->update([
                    'status' => 'refused'
                ]);
            }

            return response()->json([
                'code' => 0
            ]);
        } catch (\Throwable $e) {
            \Log::info($data);
            \Log::error($e->getMessage());
            return response()->json([
                'code' => 500
            ], 500);
        }
    }

    public function checkNotification(Request $request)
    {
        return response()->json([
            'code' => 0,
        ]);
    }

    public function payFailNotification(Request $request)
    {
        try {
            $data = $request->all();
            $this->savePayment($data);

            return response()->json([
                'code' => 0,
            ]);
        } catch (\Throwable $e) {
            \Log::info($data);
            \Log::error($e);
            return response()->json([
                'code' => 500
            ], 500);
        }
    }

    private function savePayment(array $data)
    {
        $subscription = Subscription::where('cp_subscription_id', $data['SubscriptionId'])->whereNotNull('cp_subscription_id')->first();

        if (! isset($subscription)) {
            $jsonData = '';
            if (isset($data['Data']) && is_string($data['Data'])) {
                $jsonData = json_decode($data['Data']);
            }
            if (isset($jsonData->subscription->id)) {
                $subscription = Subscription::whereId($jsonData->subscription->id)->first();
            }

            if (! isset($subscription)) {
                $subscription = Subscription::whereId($data['AccountId'])->first();
            }
        }

        if (! isset($subscription)) {
            throw new NoticeException('Не найден абонемент. Transaction Id: ' . $data['TransactionId']);
        }

        if (! isset($subscription->customer)) {
            throw new NoticeException('Не найден клиент. Transaction Id: ' . $data['TransactionId']);
        }

        $subscriptionData = [
            'from' => null,
            'to' => null,
        ];
        if ($data['Status'] == 'Completed') {
            $updateData = [
                'status' => 'paid',
            ];
            if ($data['SubscriptionId']) {
                $subscriptionData = [
                    'from' => Carbon::createFromFormat('Y-m-d H:i:s', $subscription->ended_at, 'Asia/Almaty'),
                    'to' => Carbon::createFromFormat('Y-m-d H:i:s', $subscription->ended_at, 'Asia/Almaty')->addMonths(1),
                ];
    
                $oldEndedAt = Carbon::createFromFormat('Y-m-d H:i:s', $subscription->ended_at, 'Asia/Almaty');
                $newEndedAt = Carbon::createFromFormat('Y-m-d H:i:s', $subscription->ended_at, 'Asia/Almaty')->addMonths(1);
                UserLog::create([
                    'subscription_id' => $subscription->id,
                    'user_id' => Auth::id() ?? null,
                    'type' => UserLog::CP_AUTO_RENEWAL,
                    'data' => [
                        'old' => $oldEndedAt,
                        'new' => $newEndedAt,
                        'request' => $data,
                    ],
                ]);
                $updateData['cp_subscription_id'] = $data['SubscriptionId'];
                $updateData['ended_at'] = $newEndedAt;
            }

            $subscription->update($updateData);
            $this->updateOrCreateCard($subscription->customer, $data);
        }

        $data['CardHolderMessage'] = Payment::ERROR_CODES[$data['ReasonCode'] ?? 0];

        $payment = Payment::updateOrCreate([
            'transaction_id' => $data['TransactionId'],
        ], [
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
            'product_id' => $subscription->product->id,
            'customer_id' => $subscription->customer->id,
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

        if (! isset($payment)) {
            throw new NoticeException('Не создался платеж. Transaction Id: ' . $data['TransactionId']);
        }
    }

    private function updateOrCreateCard(Customer $customer, array $data)
    {
        $customer->cards()->updateOrCreate([
            'token' => $data['Token'],
            'cp_account_id' => $data['AccountId'],
        ],
        [
            'cp_account_id' => $data['AccountId'],
            'token' => $data['Token'],
            'first_six' => $data['CardFirstSix'] ?? null,
            'last_four' => $data['CardLastFour'] ?? null,
            'exp_date' => $data['CardExpDate'] ?? null,
            'type' => $data['CardType'] ?? null,
            'name' => $data['Name'] ?? 'Default',

        ]);
    }

    public function confirmNotification(Request $request)
    {
        return response()->json([
            'code' => 0,
        ]);
    }

    public function refundNotification(Request $request)
    {
        return response()->json([
            'code' => 0,
        ]);
    }

    public function cancelNotification(Request $request)
    {
        return response()->json([
            'code' => 0,
        ]);
    }
}
