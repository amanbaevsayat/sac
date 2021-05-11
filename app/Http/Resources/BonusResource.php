<?php

namespace App\Http\Resources;

use App\Models\Subscription;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BonusResource extends JsonResource
{
    /**
     * @param  Request $request
     * @return array
     * @throws \JsonException
    */
    public function toArray($request): array
    {
        return [
            'product_id' => $this->product_id,
            'payment_type_id' => $this->payment_type_id,
            'is_active' => $this->is_active,
            'type' => $this->type,
            'amount' => $this->amount ?? 0,
        ];
    }
}
