<?php

namespace App\Filters;

use App\Models\Customer;

class SubscriptionFilter extends BaseFilter
{
    public function defaultFilter($filter, $value)
    {
        $data = [
            // 'customer_id',
        ];

        if (in_array($filter, $data)) {
            $this->builder->where($filter, 'LIKE', "%{$value}%");
        } else {
            $this->builder->where($filter, $value);
        }

        return $this->builder;
    }

    public function page($value)
    {
        
    }

    public function paymentType($value)
    {
        if (is_array($value)) {
            return $this->builder->whereIn('payment_type', $value);
        } else {
            return $this->builder->where('payment_type', $value);
        }
    }

    public function customerNameOrPhone($value)
    {
        if ($value) {
            $customerIds = Customer::where('name', 'LIKE', "%{$value}%")->orWhere('phone', 'LIKE', "%{$value}%")->pluck('id')->toArray();
            // dd($customerIds);
            return $this->builder->whereIn('customer_id', $customerIds);
        }
    }
}
