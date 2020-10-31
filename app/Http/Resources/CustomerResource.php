<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string phones
 */
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
            'remark_id' => [
                'type' => 'select',
                'collection' => 'remarks',
                'value' => $this->remark_id,
            ],
        ];
    }
}
