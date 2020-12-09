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
                'payment_types' => Subscription::PAYMENT_TYPE,
                'statuses' => Subscription::STATUSES,
            ],
            'dataTitles' => [
                // [
                //     'title' => 'ID',
                //     'key' => 'id',
                // ],
                [
                    'title' => 'Клиенты',
                    // 'key' => 'customer_id',
                ],
                [
                    'title' => 'Телефон',
                    // 'key' => 'customers.phone',
                ],
                [
                    'title' => 'Ост. дней',
                    'key' => 'ended_at',
                ],
                [
                    'title' => 'Тип оплаты',
                    'key' => 'payment_type',
                ],
                [
                    'title' => 'Дата окончания',
                    'key' => 'ended_at',
                ],
                [
                    'title' => 'Статус абонемента',
                    'key' => 'status',
                ],
                [
                    'title' => 'Услуга',
                    'key' => 'product_id',
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
