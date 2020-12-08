<?php

namespace App\Http\Resources;

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
                'value' => $this->id,
            ],
            'customer' => [
                'id' => $this->customer->id,
                'title' => $this->customer->name,
                'type' => 'customer-link',
                'value' => route('customers.show', [$this->customer->id]),
            ],
            'subscription_id' => [
                'title' => $this->subscription_id ?? null,
                'type' => 'link',
                'value' => isset($this->subscription_id) ? route('subscriptions.show', [$this->subscription_id]) : null,
            ],
            'payment_type' => [
                'type' => 'select',
                'collection' => 'payment_types',
                'value' => $this->type,
            ],
            'amount' => [
                'type' => 'input',
                'value' => $this->amount,
            ],
            'status' => [
                'type' => 'select',
                'collection' => 'statuses',
                'value' => $this->status,
            ],
            'recurrent' => [
                'type' => 'input',
                'value' => $this->recurrent,
            ],
            'start_date' => [
                'type' => 'date',
                'value' => $this->start_date,
            ],
            'interval' => [
                'type' => 'input',
                'value' => $this->interval,
            ],
            'period' => [
                'type' => 'input',
                'value' => $this->period
            ],
        ];
    }
}
