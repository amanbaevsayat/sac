<?php

namespace App\Filters;

class UserFilter extends BaseFilter
{
    public function defaultFilter($filter, $value)
    {
        $data = [
            // 'code',
            // 'title',
            // 'description',
            // 'price',
            // 'trial_price',
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
