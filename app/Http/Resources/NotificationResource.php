<?php

namespace App\Http\Resources;

use App\Models\Notification;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * @param  Request $request
     * @return array
     * @throws \JsonException
     */
    public function toArray($request): array
    {
        if (!$this->subscription) {
            return [
                'id' => [
                    'value' => $this->id,
                    'type' => 'hidden',
                ],
            ];
        }
        $typeEmpty = false;
        $types = $request->type;
        if (! $types) {
            $types = Notification::NOTIFICATION_TYPES;
            $typeEmpty = true;
        } else if (! is_array($types)) {
            $types = [$types];
        }
        setlocale(LC_TIME, 'ru_RU.UTF-8');
        Carbon::setLocale(config('app.locale'));
        $data = [
            'id' => [
                'value' => $this->id,
                'type' => 'hidden',
            ],
            'customer' => [
                'id' => $this->subscription->customer->id ?? null,
                'title' => $this->subscription->customer->name ?? null,
                'type' => 'customer-link',
                'value' => isset($this->subscription->customer->id) ? route('customers.show', [$this->subscription->customer->id]) : null,
            ],
            'customers.phone' => [
                'value' => $this->subscription->customer->phone ?? null,
                // 'type' => 'input',
            ],
        ];

        // if (in_array(Notification::TYPE_SUBSCRIPTION_ERRORS, $types) || in_array(Notification::TYPE_FIRST_SUBSCRIPTION_ERRORS, $types) || $typeEmpty) {
        $data['product_id'] = [
            'value' => $this->subscription->product->title ?? null,
        ];
        // }

        $data['days_left'] = [
            'value' => $this->subscription->daysLeft(),
            'textAlign' => 'center',
        ];
        if (! in_array(Notification::TYPE_ENDED_TRIAL_PERIOD, $types) || $typeEmpty) {
            $data['payments'] = [
                'value' => $this->subscription->payments->where('status', 'Completed')->count() ?? 0,
                'textAlign' => 'center',
            ];
        }
        
        $data['started_at'] = [
            'value' => strftime('%d %b', (new \DateTime($this->subscription->started_at))->getTimestamp()),
        ];
        $data['ended_at'] = [
            'value' => strftime('%d %b', (new \DateTime($this->subscription->getEndDate()))->getTimestamp()),
        ];
        $data['status'] = [
            'collection' => 'statuses',
            'type' => 'select',
            'value' => $this->subscription->status ?? null,
        ];

        if (in_array(Notification::TYPE_CANCEL_SUBSCRIPTION, $types) || $typeEmpty) {
            $data['created_at'] = [
                'value' => ($this->type == Notification::TYPE_CANCEL_SUBSCRIPTION) ? strftime('%d %b, %H:%M', (new \DateTime($this->created_at))->getTimestamp()) : null,
            ];
        }
        if (in_array(Notification::TYPE_SUBSCRIPTION_ERRORS, $types) || in_array(Notification::TYPE_FIRST_SUBSCRIPTION_ERRORS, $types) || $typeEmpty) {
            $data['error_description'] = [
                'value' => $this->data['error_description'] ?? null,
            ];
            $data['paided_at'] = [
                'value' => Carbon::parse($this->data['paided_at'] ?? null)->isoFormat('DD MMM YYYY, HH:mm'),
            ];
        }

        $data['in_process'] = [
            'value' => $this->in_process,
            'type' => 'checkbox'
        ];

        if (in_array(Notification::TYPE_CANCEL_SUBSCRIPTION, $types) || in_array(Notification::TYPE_SUBSCRIPTION_ERRORS, $types) || in_array(Notification::TYPE_FIRST_SUBSCRIPTION_ERRORS, $types) || $typeEmpty) {
            
            $data['processed'] = [
                'value' => $this->processed,
                'type' => 'checkbox',
                'class' => 'silver'
            ];
        }

        return $data;
    }
}
