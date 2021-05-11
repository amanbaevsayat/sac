<?php

namespace App\Filters;

class CustomerFilter extends BaseFilter
{
    public function defaultFilter($filter, $value)
    {
        $data = [
            'name',
            'phone',
            'email',
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

    public function remarkId($value)
    {
        if (is_array($value)) {
            return $this->builder->whereIn('remark_id', $value);
        } else {
            return $this->builder->where('remark_id', $value);
        }
    }

    public function customerNameOrPhone($value)
    {
        if ($value) {
            $phone = preg_replace('/[^0-9]/', '', $value);
            $query = "(name like '%{$value}%'";
            if (!empty($phone)){
                $query .= " OR phone like '%{$phone}%' OR '{$phone}' LIKE CONCAT('%', phone, '%')";
            }
            $query .= ")";
            return $this->builder->whereRaw($query);
        }
    }
}
