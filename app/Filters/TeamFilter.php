<?php

namespace App\Filters;

class TeamFilter extends BaseFilter
{
    public function defaultFilter($filter, $value)
    {
        $data = [
            'name',
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
