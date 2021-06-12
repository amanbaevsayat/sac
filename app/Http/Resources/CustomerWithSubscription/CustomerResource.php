<?php

namespace App\Http\Resources\CustomerWithSubscription;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'comments' => $this->comments,
            'subscriptions' => SubscriptionResource::collection($this->subscriptions),
        ];

        $customerCard = $this->cards->first();

        if (isset($customerCard)) {
            $data['card'] = [
                'id' => $customerCard->id,
                'type' => $customerCard->type,
                'last_four' => $customerCard->last_four,
            ];
        }

        return $data;
    }
}
