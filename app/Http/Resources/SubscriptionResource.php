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
                // 'type' => 'hidden',
            ],
            'customer' => [
                'id' => $this->customer->id,
                'title' => $this->customer->name,
                'type' => 'customer-link',
                'subscriptionId' => $this->id ?? null,
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
            'started_at' => [
                'value' => strftime('%d %b', (new \DateTime($this->started_at))->getTimestamp()),
            ],
            'ended_at' => [
                'value' => strftime('%d %b (%H:%M)', (new \DateTime($this->getEndDate()))->getTimestamp()),
            ],
            'status' => [
                'collection' => 'statuses',
                'type' => 'select',
                'value' => $this->status ?? null,
            ],
            'payments' => [
                'value' => $this->payments->where('status', 'Completed')->count() ?? 0,
                'textAlign' => 'center',
            ],
            'product' => [
                'value' => $this->product->title ?? null,
                // 'type' => 'link',
                // 'value' => $this->product_id ? route('products.show', [$this->product_id]) : null,
            ],
            'cp_subscription_id' => [
                'value' => $this->cp_subscription_id ?? null,
                // 'type' => 'link',
                // 'value' => $this->product_id ? route('products.show', [$this->product_id]) : null,
            ],
            'user_id' => [
                'value' => $this->user->name ?? null,
                // 'type' => 'link',
                // 'value' => $this->product_id ? route('products.show', [$this->product_id]) : null,
            ],
        ];
    }
}
