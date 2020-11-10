<?php

namespace App\Filters;

class PaymentFilter extends BaseFilter
{
    public function defaultFilter($filter, $value)
    {
        $data = [
            // 'customer_id',
            // 'subscription_id',
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
}
