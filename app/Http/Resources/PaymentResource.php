<?php

namespace App\Http\Resources;

use App\Models\Subscription;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * @param  Request $request
     * @return array
     * @throws \JsonException
    */
    public function toArray($request): array
    {
        return [
            'id' => [
                'id' => $this->id,
                'title' => $this->id,
                'type' => 'link',
                'value' => route('payments.show', [$this->id]),
            ],
            // 'subscription_id' => [
            //     'value' => $this->subscription->id,
            // ],
            'customer' => [
                'id' => $this->customer->id ?? null,
                'title' => $this->customer->name ?? null,
                'type' => 'customer-link',
                'value' => isset($this->customer->id) ? route('customers.show', [$this->customer->id]) : null,
            ],
            'customer_phone' => [
                'value' => $this->customer->phone ?? null,
            ],
            'product_id' => [
                'value' => $this->subscription->product->title ?? null,
            ],
            'payment_type' => [
                'value' => Subscription::PAYMENT_TYPE[$this->type],
            ],
            'amount' => [
                // 'type' => 'input',
                'value' => $this->amount * ($this->quantity ?? 1),
            ],
            'payments' => [
                'value' => (isset($this->subscription) && isset($this->subscription->payments)) ? ($this->subscription->payments->where('status', 'Completed')->count() ?? 0) : null,
                'textAlign' => 'center',
            ],
            'status' => [
                'value' => Payment::STATUSES[$this->status] ?? $this->status,
            ],
            'errors' => [
                'value' => $this->data['cloudpayments']['CardHolderMessage'] ?? null,
            ],
            'paided_at' => [
                'value' => Carbon::parse($this->paided_at)->isoFormat('DD MMM YYYY, HH:mm'),
            ],
            'from' => [
                'value' => isset($this->data['subscription']['from']) ? Carbon::parse($this->data['subscription']['from'])->isoFormat('DD MMM, YY') : null,
            ],
            'to' => [
                'value' => isset($this->data['subscription']['to']) ? Carbon::parse($this->data['subscription']['to'])->isoFormat('DD MMM, YY') : null,
            ],
            'transaction_id' => [
                'value' => $this->transaction_id,
            ],
        ];
    }
}
