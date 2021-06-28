<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
                // 'type' => 'hidden',
            ],
            'name' => [
                'value' => $this->name,
            ],
            'account' => [
                'value' => $this->account,
            ],
            'email' => [
                'value' => $this->email,
            ],
            'role_id' => [
                'value' => $this->role->title ?? null,
            ],
            'phone' => [
                'value' => $this->phone,
            ],
            'is_active' => [
                'value' => $this->is_active ? 'Активный' : 'Не активный',
            ],
        ];
    }
}
