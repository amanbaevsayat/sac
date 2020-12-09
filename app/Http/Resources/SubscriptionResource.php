<?php

namespace App\Http\Resources;

use App\Models\Subscription;
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
        setlocale(LC_TIME, 'ru_RU.UTF-8');
        Carbon::setLocale(config('app.locale'));
        return [
            'id' => [
                'value' => $this->id,
                'type' => 'hidden',
            ],
            'customer' => [
                'id' => $this->customer->id,
                'title' => $this->customer->name,
                'type' => 'customer-link',
                'value' => route('customers.show', [$this->customer->id]),
            ],
            'customers.phone' => [
                'value' => $this->customer->phone,
                // 'type' => 'input',
            ],
            'days_left' => [
                'value' => $this->daysLeft(),
            ],
            'payment_type' => [
                'value' => Subscription::PAYMENT_TYPE[$this->payment_type],
            ],
            'ended_at' => [
                'value' => strftime('%d %b', (new \DateTime($this->ended_at))->getTimestamp()),
            ],
            'status' => [
                'value' => Subscription::STATUSES[$this->status],
            ],
            'product' => [
                'value' => $this->product->title ?? null,
                // 'type' => 'link',
                // 'value' => $this->product_id ? route('products.show', [$this->product_id]) : null,
            ],
        ];
    }
}
