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
            if ($value[0] == 'undefined') {
                return $this->builder;
            }
            return $this->builder->whereIn('payment_type', $value);
        } else {
            if ($value == 'undefined') {
                return $this->builder;
            }
            return $this->builder->where('payment_type', $value);
        }
    }

    public function status($value)
    {
        if (is_array($value)) {
            if ($value[0] == 'undefined') {
                return $this->builder;
            }
            return $this->builder->whereIn('status', $value);
        } else {
            if ($value == 'undefined') {
                return $this->builder;
            }
            return $this->builder->where('status', $value);
        }
    }

    public function customerNameOrPhone($value)
    {
        if ($value) {
            $customersQuery = Customer::query();
            $phone = preg_replace('/[^0-9]/', '', $value);
            $query = "(name like '%{$value}%'";
            if (!empty($phone)){
                $query .= " OR phone like '%{$phone}%' OR '{$phone}' LIKE CONCAT('%', phone, '%')";
            }
            $query .= ")";
            $customerIds = $customersQuery->whereRaw($query)->pluck('id')->toArray();
            return $this->builder->whereIn('customer_id', $customerIds);
        }
    }

    public function sort($value)
    {
        preg_match('#\((.*?)\)#', $value, $match);
        $name = preg_replace("/\([^)]+\)/", "", $value);
        if ($match[1] == 'asc') {
            if ($name == 'ended_at') {
                $this->builder->orderByRaw(
                    "CASE WHEN tries_at > ended_at THEN tries_at ELSE ended_at END ASC"
                );
            } else {
                $this->builder->orderBy($name);
            }
        } elseif ($match[1] == 'desc') {
            if ($name == 'ended_at') {
                $this->builder->orderByRaw(
                    "CASE WHEN tries_at > ended_at THEN tries_at ELSE ended_at END DESC"
                );
            } else {
                $this->builder->orderBy($name, 'desc');
            }
        }
        return $this->builder;
    }
}
