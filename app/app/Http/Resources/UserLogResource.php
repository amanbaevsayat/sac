<?php

namespace App\Http\Resources;

use App\Models\Subscription;
use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserLogResource extends JsonResource
{
    /**
     * @param  Request $request
     * @return array
     * @throws \JsonException
     */
    public function toArray($request): array
    {
        setlocale(LC_TIME, 'ru_RU.UTF-8');
        Carbon::setLocale(config('app.locale'));
        return [
            'id' => [
                'value' => $this->id,
                'type' => 'hidden',
            ],
            'subscription_id' => [
                'value' => $this->subscription_id ?? null,
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
            'type' => [
                'value' => UserLog::TYPES[$this->type],
                // 'type' => 'input',
            ],
            'data' => [
                'value' => $this->getDescription(),
                'type' => 'html',
            ],
            'userName' => [
                'value' => $this->user->account ?? null,
                // 'type' => 'input',
            ],
            'created_at' => [
                'value' => strftime('%d %b (%H:%M)', (new \DateTime($this->created_at))->getTimestamp()),
            ],
        ];
    }
}
