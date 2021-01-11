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
            'customer' => [
                'id' => $this->customer->id,
                'title' => $this->customer->name,
                'type' => 'customer-link',
                'value' => route('customers.show', [$this->customer->id]),
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
            'status' => [
                'value' => Payment::STATUSES[$this->status] ?? $this->status,
            ],
            'errors' => [
                'value' => $this->data['cloudpayments']['CardHolderMessage'] ?? null,
            ],
            'paided_at' => [
                'value' => Carbon::parse($this->paided_at ?? $this->created_at)->isoFormat('DD MMM YYYY, HH:mm'),
            ],
            // 'interval' => [
            //     'type' => 'input',
            //     'value' => $this->interval,
            // ],
            // 'period' => [
            //     'type' => 'input',
            //     'value' => $this->period
            // ],
        ];
    }
}
