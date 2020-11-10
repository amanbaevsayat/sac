<?php

namespace App\Http\Resources;

use App\Models\Remark;
use App\Models\Subscription;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @property string phones
 */
class SubscriptionCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => SubscriptionResource::collection($this->collection),
            'others' => [
                'statuses' => Subscription::STATUSES,
            ],
            'dataTitles' => [
                [
                    'title' => 'ID',
                    'key' => 'id',
                ],
                [
                    'title' => 'Продукт',
                    'key' => 'product_id',
                ],
                [
                    'title' => 'Дата старта',
                    'key' => 'started_at',
                ],
                [
                    'title' => 'Дата заморозки',
                    'key' => 'paused_at',
                ],
                [
                    'title' => 'Дата окончания',
                    'key' => 'ended_at',
                ],
                [
                    'title' => 'Цена',
                    'key' => 'amount',
                ],
                [
                    'title' => 'Описание',
                    'key' => 'description',
                ],
                [
                    'title' => 'Статус',
                    'key' => 'status',
                ],
            ],
            'pagination' => [
                'current_page' => $this->currentPage(),
                'first_page_url' => $this->url(1),
                'from' => $this->firstItem(),
                'last_page' => $this->lastPage(),
                'last_page_url' => $this->url($this->lastPage()),
                'next_page_url' => $this->nextPageUrl(),
                'path' => $this->path(),
                'per_page' => $this->perPage(),
                'prev_page_url' => $this->previousPageUrl(),
                'to' => $this->lastItem(),
                'total' => $this->total(),
            ],
        ];
    }
}
