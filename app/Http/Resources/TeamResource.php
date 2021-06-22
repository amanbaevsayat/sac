<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
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
                'type' => 'hidden',
            ],
            'name' => [
                'title' => $this->name,
                'type' => 'link',
                'value' => route('teams.edit', [$this->id]),
            ],
        ];
    }
}
