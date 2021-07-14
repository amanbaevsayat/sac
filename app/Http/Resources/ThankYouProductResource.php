<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThankYouProductResource extends JsonResource
{
    /**
     * @param  Request $request
     * @return array
     * @throws \JsonException
     */
    public function toArray($request): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->data['image'] ?? '',
            'phone' => str_replace(' ', '', ($this->data['phone'] ?? '')),
        ];
    }
}
