<?php

namespace App\Http\Resources;

use App\Models\Payment;
use App\Models\Remark;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @property string phones
 */
class PaymentCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => PaymentResource::collection($this->collection),
            'others' => [
                'statuses' => Payment::STATUSES,
            ],
            'dataTitles' => [
                [
                    'title' => 'ID',
                    'key' => 'id',
                ],
                [
                    'title' => 'Клиент',
                    'key' => 'customer_id',
                ],
                [
                    'title' => 'Подписка',
                    'key' => 'subscription_id',
                ],
                [
                    'title' => 'Тип',
                    'key' => 'type',
                ],
                [
                    'title' => 'Сумма',
                    'key' => 'amount',
                ],
                [
                    'title' => 'Статус',
                    'key' => 'status',
                ],
                [
                    'title' => 'Рекуррентный платеж',
                    'key' => 'recurrent',
                ],
                [
                    'title' => 'Дата списания',
                    'key' => 'start_date',
                ],
                [
                    'title' => 'Интервал списания',
                    'key' => 'interval',
                ],
                [
                    'title' => 'Период списания',
                    'key' => 'period',
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
