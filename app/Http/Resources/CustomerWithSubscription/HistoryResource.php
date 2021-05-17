<?php

namespace App\Http\Resources\CustomerWithSubscription;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Datetime;

class HistoryResource extends JsonResource
{
    /**
     * @param  Request $request
     * @return array
     * @throws \JsonException
     */
    public function toArray($request): array
    {
        $start = $this->data['subscription']['from'] ?? $this->data['subscription']['first_ended_at'] ?? null;
        $end = $this->data['subscription']['to'] ?? $this->data['subscription']['second_ended_at'] ?? null;
        return [
            'dates' => [
                'start' => isset($start) ? date(DATE_ATOM, strtotime($start)) : null,
                'end' => isset($end) ? date(DATE_ATOM, strtotime($end)) : null,
            ]
        ];
    }
}
