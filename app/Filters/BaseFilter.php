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
}
