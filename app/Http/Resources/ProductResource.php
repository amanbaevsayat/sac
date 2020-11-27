<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            // 'code' => [
            //     'type' => 'input',
            //     'value' => $this->code,
            // ],
            'title' => [
                'type' => 'input',
                'value' => $this->title,
            ],
            'description' => [
                'type' => 'input',
                'value' => $this->description,
            ],
        ];
    }
}
