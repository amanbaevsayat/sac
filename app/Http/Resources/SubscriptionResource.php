<?php

namespace App\Http\Resources;

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
            'product' => [
                'title' => $this->product->title,
                'type' => 'link',
                'value' => route('products.show', [$this->product->id]),
            ],
            'started_at' => [
                'type' => 'date',
                'value' => $this->started_at,
            ],
            'paused_at' => [
                'type' => 'date',
                'value' => $this->paused_at,
            ],
            'ended_at' => [
                'type' => 'date',
                'value' => $this->ended_at,
            ],
            'amount' => [
                'type' => 'input',
                'value' => $this->amount,
            ],
            'description' => [
                'type' => 'input',
                'value' => $this->description,
            ],
            'status' => [
                'type' => 'select',
                'collection' => 'statuses',
                'value' => $this->status,
            ],
            'payments' => [
                'title' => 'Платежи',
                'type' => 'link',
                'value' => route('payments.index', ['subscription_id' => $this->id]),
            ],
        ];
    }
}
