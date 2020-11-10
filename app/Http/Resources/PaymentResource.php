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
            'customer_id' => [
                'title' => $this->customer->name,
                'type' => 'link',
                'value' => route('customers.show', [$this->customer->id]),
            ],
            'subscription_id' => [
                'title' => $this->subscription_id,
                'type' => 'link',
                'value' => route('subscriptions.show', [$this->subscription->id]),
            ],
            'type' => [
                'type' => 'input',
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
