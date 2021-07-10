<?php

namespace App\Http\Resources;

use App\Models\Reason;
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
            'data' => SubscriptionResource::collection($this->collection),
            'others' => [
                'payment_types' => Subscription::PAYMENT_TYPE,
                'statuses' => Subscription::STATUSES,
                'reasons' => Reason::all()->groupBy('product_id')->transform(function($item) {
                    return $item->pluck('title', 'id');
                }),
            ],
            'dataTitles' => [
                [
                    'title' => 'ID',
                ],
                [
                    'title' => 'Клиенты',
                ],
                [
                    'title' => 'Телефон',
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
                    'title' => 'Дата старта',
                    'key' => 'started_at',
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
                    'title' => 'Кол-во платежей',
                    'key' => 'payments',
                ],
                [
                    'title' => 'Услуга',
                    'key' => 'product_id',
                ],
                [
                    'title' => 'Cloudpayment ID',
                    'key' => 'cp_subscription_id',
                ],
                [
                    'title' => 'Создал',
                    'key' => 'user_id',
                ],
                [
                    'title' => 'Причина отказа',
                    'key' => 'reason_id',
                    'display' => 'none',
                ],
                [
                    'title' => 'ID продукта',
                    'key' => 'product_id',
                    'display' => 'none',
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
