<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'customers.phone' => [
                'value' => $this->customer->phone,
                'type' => 'input',
            ],
            'days_left' => [
                'value' => $this->daysLeft(),
            ],
            'payment_type' => [
                'type' => 'select',
                'collection' => 'payment_types',
                'value' => $this->payment_type,
            ],
            'ended_at' => [
                'type' => 'date',
                'value' => $this->ended_at,
            ],
            'status' => [
                'type' => 'select',
                'collection' => 'statuses',
                'value' => $this->status,
            ],
            'product' => [
                'value' => $this->product->title ?? null,
                // 'type' => 'link',
                // 'value' => $this->product_id ? route('products.show', [$this->product_id]) : null,
            ],
            'payments' => [
                'title' => 'Платежи',
                'type' => 'link',
                'value' => route('payments.index', ['subscription_id' => $this->id]),
            ],
        ];
    }
}
