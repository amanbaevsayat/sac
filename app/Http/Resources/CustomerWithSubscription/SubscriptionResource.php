<?php

namespace App\Http\Resources\CustomerWithSubscription;

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
        $recurrentPayment = $this->payments()->where('status', 'new')->where('type', 'cloudpayments')->first();

        $data = [
            'id' => $this->id,
            'product_id'    => $this->product_id,
            'price'      => $this->price,
            'payment_type'  => $this->payment_type,
            'started_at'    => $this->started_at,
            'paused_at'     => $this->paused_at,
            'ended_at'      => $this->ended_at,
            'status'        => $this->status,
            'description'   => $this->description,
            'payments'      => PaymentResource::collection($this->payments),
            'product'       => [
                'id'    => $this->product->id,
                'title' => $this->product->title,
                'price' => $this->price,
            ],
            'newPayment' => [ // Шаблон для фронта
                'quantity' => 1,
                'check' => null,
            ]
        ];

        if ($recurrentPayment) {
            $data['recurrent'] = [
                'link' => route('cloudpayments.show_widget', ['slug' => $recurrentPayment->slug]),
            ];
        }

        return $data;
    }
}
