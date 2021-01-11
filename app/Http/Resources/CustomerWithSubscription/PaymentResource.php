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
            'url' => route('payments.show', [$this->id]),
            'type' => $this->type,
            'status' => $this->status,
            'check' => $this->data['check'] ?? null,
            'quantity' => $this->quantity,
            'user' => [
                'url' => $this->user_id ? route('users.edit', [$this->user_id]) : null,
                'name' => $this->user->account ?? null,
            ],
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
                $title = "{$paidedAt}, успешно оплатил по подписке {$this->subscription->price} тг. " . Carbon::parse(date(DATE_ATOM, strtotime($this->data['subscription']['from'] ?? $this->data['subscription']['first_ended_at'] ?? null)))->isoFormat('DD MMM YYYY') . ' - ' . Carbon::parse(date(DATE_ATOM, strtotime($this->data['subscription']['to'] ?? $this->data['subscription']['second_ended_at'] ?? null)))->isoFormat('DD MMM YYYY');
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
                $title = "{$updatedAt}, прямой перевод на сумму {$amount} тг. " . Carbon::parse(date(DATE_ATOM, strtotime($this->data['subscription']['from'] ?? $this->data['subscription']['first_ended_at'] ?? null)))->isoFormat('DD MMM YYYY') . ' - ' . Carbon::parse(date(DATE_ATOM, strtotime($this->data['subscription']['to'] ?? $this->data['subscription']['second_ended_at'] ?? null)))->isoFormat('DD MMM YYYY');
            } elseif ($this->status == 'Declined') {
                $title = "{$updatedAt}, не оплатил переводом на сумму {$this->subscription->price} тг";
            }
        } elseif ($this->type == 'frozen') {
            if ($this->status == 'frozen') {
                $title = "{$createdAt}, абонемент заморожен с " . Carbon::parse(date(DATE_ATOM, strtotime($this->data['subscription']['from'])))->isoFormat('DD MMM YYYY') . ' - по ' . Carbon::parse(date(DATE_ATOM, strtotime($this->data['subscription']['to'])))->isoFormat('DD MMM YYYY');
            } elseif ($this->status == 'new') {
                $amount = $this->subscription->price * $this->quantity;
                $title = "{$createdAt}, абонемент заморожен с " . Carbon::parse(date(DATE_ATOM, strtotime($this->data['subscription']['from'])))->isoFormat('DD MMM YYYY') . ' - по ' . Carbon::parse(date(DATE_ATOM, strtotime($this->data['subscription']['to'])))->isoFormat('DD MMM YYYY');
            }
        } else {
            $title = '';
        }
        $data['title'] = $title ?? null;
        return $data;
    }
}
