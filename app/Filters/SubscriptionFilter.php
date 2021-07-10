<?php

namespace App\Filters;

use App\Models\Customer;
use Carbon\Carbon;

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

    // public function payments($value)
    // {
    //     // dd($value);
    // }

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

    public function productId($value)
    {
        if (is_array($value)) {
            return $this->builder->whereIn('product_id', $value);
        } else {
            return $this->builder->where('product_id', $value);
        }
    }

    public function teamId($value)
    {
        if (is_array($value)) {
            if ($value[0] == 'undefined') {
                return $this->builder;
            }
            if (in_array(9999, $value)) {
                return $this->builder->where('team_id', null);
            } else {
                return $this->builder->whereIn('team_id', $value);
            }
        } else {
            if ($value == 'undefined') {
                return $this->builder;
            }
            if (9999 == $value) {
                return $this->builder->where('team_id', null);
            } else {
                return $this->builder->where('team_id', $value);
            }
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
            return $this->builder->whereIn('customer_id', $customerIds);
        }
    }

    public function sort($value)
    {
        preg_match('#\((.*?)\)#', $value, $match);
        $name = preg_replace("/\([^)]+\)/", "", $value);

        if ($match[1] == 'asc' && $name != 'payments') {
            if ($name == 'ended_at') {
                $this->builder->orderByRaw(
                    "CASE WHEN tries_at > ended_at THEN tries_at ELSE ended_at END ASC"
                );
            } else {
                $this->builder->orderBy($name);
            }
        } elseif ($match[1] == 'desc' && $name != 'payments') {
            if ($name == 'ended_at') {
                $this->builder->orderByRaw(
                    "CASE WHEN tries_at > ended_at THEN tries_at ELSE ended_at END DESC"
                );
            } else {
                $this->builder->orderBy($name, 'desc');
            }
        } elseif ($name == 'payments') {
            $this->builder->withCount(['payments' => function ($query) {
                $query->where('status', 'Completed');
            }])->orderBy('payments_count', 'desc');
        }

        return $this->builder;
    }

    public function fromStartDate($value)
    {
        if (is_numeric(strtotime($value))) {
            $day = Carbon::parse($value)->startOfDay();
            $this->builder->whereDate('started_at', '>=', $day->format('Y-m-d 00:00:00'));
        }
    }

    public function toStartDate($value)
    {
        if (is_numeric(strtotime($value))) {
            $day = Carbon::parse($value)->endOfDay();
            $this->builder->whereDate('started_at', '<=', $day->format('Y-m-d 23:59:59'));
        }
    }
}
