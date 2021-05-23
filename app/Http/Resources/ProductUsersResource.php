<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductUsersResource extends JsonResource
{
    /**
     * @param  Request $request
     * @return array
     * @throws \JsonException
    */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'stake' => $this->pivot->stake,
            'employment_at' => $this->pivot->employment_at ? date(DATE_ATOM, strtotime($this->pivot->employment_at)) : null,
        ];
    }
}
