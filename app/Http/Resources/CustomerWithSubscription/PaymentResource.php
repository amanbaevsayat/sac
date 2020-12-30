<?php

namespace App\Http\Resources\CustomerWithSubscription;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

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
        $updatedAt = Carbon::parse($this->updated_at)->isoFormat('DD MMM YYYY, HH:mm');
        $createdAt = Carbon::parse($this->created_at)->isoFormat('DD MMM YYYY, HH:mm');
        $paidedAt = Carbon::parse($this->paided_at)->isoFormat('DD MMM YYYY, HH:mm');

        if ($this->type == 'cloudpayments') {
            if ($this->status == 'new') {
                $title = "{$createdAt}, создана подписка оператором на сумму {$this->subscription->price} тг";
            } elseif ($this->status == 'Completed') {
                $title = "{$paidedAt}, успешно оплатил по подписке {$this->subscription->price} тг";
            } elseif ($this->status == 'Declined') {
                $description = $this->data['cloudpayments']['CardHolderMessage'] ?? null;
                $title = "{$updatedAt}, ошибка при оплате подписки на сумму {$this->subscription->price} тг (Описание: {$description})";
            } elseif ($this->status == 'Authorized') {
                $description = $this->data['cloudpayments']['CardHolderMessage'] ?? null;
                $title = "{$updatedAt}, успешно оплатил по подписке {$this->subscription->price} тг. Осталось подтвердить оплату. (Описание: {$description})";
            }
        } elseif ($this->type == 'transfer') {
            if ($this->status == 'new') {
                $title = "{$createdAt}, ожидаю оплату переводом на сумму {$this->subscription->price} тг";
            } elseif ($this->status == 'Completed') {
                $amount = $this->subscription->price * $this->quantity;
                $title = "{$updatedAt}, прямой перевод на сумму {$amount} тг";
            } elseif ($this->status == 'Declined') {
                $title = "{$updatedAt}, не оплатил переводом на сумму {$this->subscription->price} тг";
            }
        } else {
            $title = '';
        }
        $data['title'] = $title ?? null;
        return $data;
    }
}
