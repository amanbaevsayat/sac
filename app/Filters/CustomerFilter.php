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
        if (is_int($value)) {
            $this->builder->where('remark_id', $value);
        } else if (is_array($value)) {
            $this->builder->whereIn('remark_id', $value);
        }
        return $this->builder;
    }

    public function sort($value)
    {
        $builder = $this->builder;
        preg_match('#\((.*?)\)#', $value, $match);
        $name = preg_replace("/\([^)]+\)/", "", $value);
        if ($match[1] == 'asc') {
            $builder->orderBy($name);
        } elseif ($match[1] == 'desc') {
            $builder->orderByDesc($name);
        }
        return $builder;
    }
}
