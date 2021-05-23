<?php

namespace App\Http\Resources;

use App\Models\Bonus;
use App\Models\Subscription;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentTypeResource extends JsonResource
{
    /**
     * @param  Request $request
     * @return array
     * @throws \JsonException
    */
    public function toArray($request): array
    {
        return [
            'type' => $this->payment_type,
            'bonuses' => [
                'firstPayment' => $this->bonuses->where('is_active', true)->where('type', Bonus::FIRST_PAYMENT)->first()->amount ?? 0,
                'repeatedPayment' => $this->bonuses->where('is_active', true)->where('type', Bonus::REPEATED_PAYMENT)->first()->amount ?? 0,
            ],
        ];
    }
}
