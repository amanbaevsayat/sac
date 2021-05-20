<?php

namespace App\Http\Resources\CustomerWithSubscription;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Datetime;

class SubscriptionResource extends JsonResource
{
    /**
     * @param  Request $request
     * @return array
     * @throws \JsonException
     */
    public function toArray($request): array
    {
        $data = [
            'id' => $this->id,
            'user_id'    => $this->user_id,
            'product_id'    => $this->product_id,
            'is_edit_ended_at'  => false,
            'price'         => $this->price,
            'payment_type'  => $this->payment_type,
            'payment_types' => $this->product->paymentTypes()->pluck('payment_type'),
            'started_at'    => $this->started_at ? date(DATE_ATOM, strtotime($this->started_at)) : null,
            'paused_at'     => $this->paused_at ? date(DATE_ATOM, strtotime($this->paused_at)) : null,
            'tries_at'      => $this->tries_at ? date(DATE_ATOM, strtotime($this->tries_at)) : null,
            'ended_at'      => $this->ended_at ? date(DATE_ATOM, strtotime($this->ended_at)) : null,
            'frozen_at'     => $this->frozen_at ? date(DATE_ATOM, strtotime($this->frozen_at)) : null,
            'defrozen_at'   => $this->defrozen_at ? date(DATE_ATOM, strtotime($this->defrozen_at)) : null,
            'next_transaction_date' => null,
            'status'        => $this->status,
            'description'   => $this->description,
            'cp_subscription_id' => $this->cp_subscription_id ?? null,
            'payments'      => PaymentResource::collection($this->payments->where('status', '!=', 'new')->sortByDesc('paided_at')),
            'recurrent'     => [
                'link'      => route('cloudpayments.show_widget', [$this->id]),
            ],
            'product'       => [
                'id'    => $this->product->id,
                'title' => $this->product->title,
                'price' => $this->price,
            ],
            'newPayment' => [ // Шаблон для фронта
                'from' => null,
                'to' => null,
                'quantity' => 1,
                'check' => null,
            ],
            'history' => HistoryResource::collection($this->payments->where('status', '!=', 'new')->sortByDesc('type'))->collection->groupBy('type'),
        ];

        return $data;
    }
}
