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
            'title' => [
                'title' => $this->title,
                'type' => 'link',
                'value' => route('products.edit', [$this->id]),
            ],
            'description' => [
                'type' => 'input',
                'value' => $this->description,
            ],
        ];
    }
}
