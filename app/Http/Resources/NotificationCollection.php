<?php

namespace App\Http\Resources;

use App\Models\Notification;
use App\Models\Subscription;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @property string phones
 */
class NotificationCollection extends ResourceCollection
{
    public function toArray($request)
    {
        $typeEmpty = false;
        $types = $request->type;
        if (! $types) {
            $types = Notification::NOTIFICATION_TYPES;
            $typeEmpty = true;
        } else if (! is_array($types)) {
            $types = [$types];
        }
        // dd($type);
        $data = [
            'data' => NotificationResource::collection($this->collection),
            'others' => [
                'payment_types' => Subscription::PAYMENT_TYPE,
                'statuses' => Subscription::STATUSES,
                'types' => Notification::TYPES,
            ],
            'dataTitles' => [
                [
                    'title' => 'Клиенты',
                ],
                [
                    'title' => 'Телефон',
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

        // if (in_array(Notification::TYPE_SUBSCRIPTION_ERRORS, $types) || in_array(Notification::TYPE_FIRST_SUBSCRIPTION_ERRORS, $types) || $typeEmpty) {
        $data['dataTitles'][] = [
            'title' => 'Услуги',
        ];
        // }

        $data['dataTitles'][] = [
            'title' => 'Ост. дней',
            'textAlign' => 'center',
        ];
        if (! in_array(Notification::TYPE_ENDED_TRIAL_PERIOD, $types) || $typeEmpty) {
            $data['dataTitles'][] = [
                'title' => 'Кол-во платежей',
                'textAlign' => 'center',
            ];
        }
        $data['dataTitles'][] = [
            'title' => 'Дата старта',
        ];
        $data['dataTitles'][] = [
            'title' => 'Дата окончания',
        ];
        $data['dataTitles'][] = [
            'title' => 'Статус абонемента',
        ];

        if (in_array(Notification::TYPE_CANCEL_SUBSCRIPTION, $types) || $typeEmpty) {
            $data['dataTitles'][] = [
                'title' => 'Дата отмены подписки',
            ];
        }
        
        if (in_array(Notification::TYPE_SUBSCRIPTION_ERRORS, $types) || in_array(Notification::TYPE_FIRST_SUBSCRIPTION_ERRORS, $types) || $typeEmpty) {
            $data['dataTitles'][] = [
                'title' => 'Описание ошибки',
            ];

            $data['dataTitles'][] = [
                'title' => 'Дата оплаты',
            ];
        }
        $data['dataTitles'][] = [
            'title' => 'В процессе',
        ];

        if (in_array(Notification::TYPE_CANCEL_SUBSCRIPTION, $types) || in_array(Notification::TYPE_SUBSCRIPTION_ERRORS, $types) || in_array(Notification::TYPE_FIRST_SUBSCRIPTION_ERRORS, $types) || $typeEmpty) {
            
            $data['dataTitles'][] = [
                'title' => 'Обработано',
            ];
        }

        return $data;
    }
}
