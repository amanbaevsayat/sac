<?php

namespace App\Filters;

use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

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

    public function customerNameOrPhone($value)
    {
        if ($value) {
            $customerIds = Customer::where('name', 'LIKE', "%{$value}%")->orWhere('phone', 'LIKE', "%{$value}%")->pluck('id')->toArray();
            return $this->builder->whereIn('customer_id', $customerIds);
        }
    }

    public function type($value)
    {
        if (is_array($value)) {
            return $this->builder->whereIn('type', $value);
        } else {
            return $this->builder->where('type', $value);
        }
    }

    public function newPayment($value)
    {
        if (filter_var($value, FILTER_VALIDATE_BOOLEAN) === true) {
            return $this->builder->whereHas('subscription', function (Builder $query) {
                $query->whereHas('payments', function (Builder $query) {
                    $query->where('status', 'Completed');
                }, '=', 1);
            });
        }
    }

    public function status($value)
    {
        if (is_array($value)) {
            return $this->builder->whereIn('status', $value);
        } else {
            return $this->builder->where('status', $value);
        }
    }

    public function from($value)
    {
        if (is_numeric(strtotime($value))) {
            $day = Carbon::parse($value)->setTimezone('Asia/Almaty')->startOfDay();
            $this->builder->where('paided_at', '>=', $day);
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

    public function to($value)
    {
        if (is_numeric(strtotime($value))) {
            $day = Carbon::parse($value)->setTimezone('Asia/Almaty')->endOfDay();
            $this->builder->where('paided_at', '<=', $day);
        }
    }

    public function sort($value)
    {
        preg_match('#\((.*?)\)#', $value, $match);
        $name = preg_replace("/\([^)]+\)/", "", $value);
        if ($match[1] == 'asc') {
            if ($name == 'paided_at') {
                $this->builder->orderByRaw(
                    "CASE WHEN paided_at = null THEN created_at ELSE paided_at END ASC"
                );
            } else if ($name == 'from' || $name == 'to') {
                $this->builder->orderBy('data->subscription->' . $name);
            } else {
                $this->builder->orderBy($name);
            }
        } elseif ($match[1] == 'desc') {
            if ($name == 'paided_at') {
                $this->builder->orderByRaw(
                    "CASE WHEN paided_at = null THEN created_at ELSE paided_at END DESC"
                );
            } else if ($name == 'from' || $name == 'to') {
                $this->builder->orderBy('data->subscription->' . $name, 'desc');
            } else {
                $this->builder->orderBy($name, 'desc');
            }
        }
        return $this->builder;
    }
}
