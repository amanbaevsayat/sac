<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

abstract class BaseFilter
{
    protected $request;
    protected $builder;

    /**
     * Filter constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param Builder $builder
     */
    public function apply(Builder $builder)
    {
        $this->builder = $builder;
        
        foreach($this->filters() as $filter => $value) {
            $filterCamelCase = Str::camel($filter);
            if($filter != "page") {
                if(method_exists($this, $filterCamelCase)) {
                    $this->$filterCamelCase($value);
                } else {
                    $this->defaultFilter($filter, $value);
                }
            }
        }
    }

    public function filters()
    {
        return $this->request->all();
    }

    public function sort($value)
    {
        $builder = $this->builder;
        preg_match('#\((.*?)\)#', $value, $match);
        $name = preg_replace("/\([^)]+\)/", "", $value);
        if ($match[1] == 'asc') {
            $builder->orderBy($name);
        } elseif ($match[1] == 'desc') {
            $builder->orderBy($name, 'desc');
        }
        return $builder;
    }
}
