<?php

namespace App\Filters;

use App\Models\Customer;
use App\Models\Subscription;

class UserLogFilter extends BaseFilter
{
    public function defaultFilter($filter, $value)
    {
        $data = [
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

    public function custom($value)
    {
        return $this->builder->where('data->old', 'refused')->whereNull('user_id');
    }

    public function productId($value)
    {
        if (is_array($value)) {
            return $this->builder->whereHas('subscription', function ($q) use ($value) {
                $q->whereIn('product_id', $value);
            });
        } else {
            return $this->builder->whereHas('subscription', function ($q) use ($value) {
                $q->where('product_id', $value);
            });
        }
    }

    public function customerNameOrPhone($value)
    {
        if ($value) {
            $customersQuery = Customer::query();
            $phone = preg_replace('/[^0-9]/', '', $value);
            $query = "(name like '%{$value}%'";
            if (!empty($phone)) {
                $query .= " OR phone like '%{$phone}%' OR '{$phone}' LIKE CONCAT('%', phone, '%')";
            }
            $query .= ")";
            $customerIds = $customersQuery->whereRaw($query)->pluck('id')->toArray();
            $subscriptionIds = Subscription::whereIn('customer_id', $customerIds)->pluck('id')->toArray();
            return $this->builder->whereIn('subscription_id', $subscriptionIds);
        }
    }
}
