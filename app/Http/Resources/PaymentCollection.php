<?php

namespace App\Http\Resources;

use App\Models\Payment;
use App\Models\Remark;
use App\Models\Subscription;
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
                'payment_types' => Subscription::PAYMENT_TYPE,
            ],
            'dataTitles' => [
                [
                    'title' => 'ID',
                    'key' => 'id',
                ],
                [
                    'title' => 'Статус абонемента',
                ],
                [
                    'title' => 'Клиент',
                    'key' => 'customer_id',
                ],
                [
                    'title' => 'Телефон',
                ],
                [
                    'title' => 'Абонемент',
                    'key' => 'subscription_id',
                ],
                [
                    'title' => 'Тип',
                    'key' => 'type',
                ],
                [
                    'title' => 'Цена',
                    'key' => 'amount',
                ],
                [
                    'title' => 'Кол-во платежей',
                    // 'key' => 'payments',
                ],
                [
                    'title' => 'Статус',
                    'key' => 'status',
                ],
                [
                    'title' => 'Описание платежа',
                    'width' => '18vw',
                ],
                [
                    'title' => 'Дата оплаты',
                    'key' => 'paided_at',
                ],
                [
                    'title' => 'С',
                    'key' => 'from',
                ],
                [
                    'title' => 'По',
                    'key' => 'to',
                ],
                [
                    'title' => 'Transaction ID',
                    'key' => 'transaction_id',
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
