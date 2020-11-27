<?php

namespace App\Http\Resources\CustomerWithSubscription;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * @param  Request $request
     * @return array
     * @throws \JsonException
     */
    public function toArray($request): array
    {
        $data = [
            'id' => $this->id,
            'type' => $this->type,
            'status' => $this->status,
            'check' => $this->data['check'] ?? null,
            'link' => route('cloudpayments.show_widget', ['slug' => $this->slug]),
            'quantity' => $this->quantity,
            'recurrent' => [
                'on' => $this->recurrent,
                'start_date' => $this->start_date,
                'interval' => $this->interval,
                'period' => $this->period,
            ],
        ];
        if ($this->type == 'cloudpayments') {
            if ($this->status == 'new') {
                $title = "{$this->created_at} создана подписка оператором на сумму {$this->subscription->price} тг";
            } elseif ($this->status == 'Completed') {
                $title = "{$this->paided_at} оплата по подписке успешно {$this->subscription->price} тг";
            } elseif ($this->status == 'Declined') {
                $title = "{$this->updated_at} ошибка при оплате подписки на сумму {$this->subscription->price} тг (Ошибка: TODO)";
            }
        } elseif ($this->type == 'transfer') {
            if ($this->status == 'new') {
                $title = "{$this->created_at} ожидаю оплату переводом на сумму {$this->subscription->price} тг";
            } elseif ($this->status == 'Completed') {
                $title = "{$this->updated_at} прямой перевод на сумму {$this->subscription->price} тг";
            } elseif ($this->status == 'Declined') {
                $title = "{$this->updated_at} не оплатил переводом на сумму {$this->subscription->price} тг";
            }
        } else {
            $title = '';
        }
        $data['title'] = $title;
        return $data;
    }
}
