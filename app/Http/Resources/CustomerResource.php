<?php

namespace App\Http\Resources;

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
        return [
            'id' => [
                'value' => $this->id,
            ],
            'name' => [
                'type' => 'input',
                'value' => $this->name,
            ],
            'phone' => [
                'type' => 'input',
                'value' => $this->phone,
            ],
            'email' => [
                'type' => 'input',
                'value' => $this->email,
            ],
            'subscriptions' => [
                'title' => 'Подписки',
                'type' => 'link',
                'value' => route('subscriptions.index', ['customer_id' => $this->id]),
            ],
        ];
    }
}
