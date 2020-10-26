<?php

namespace App\Services;

class SortService
{
    public function sortable(string $property, array $params)
    {
        $params['orderBy'] = $property;
        $params['orderTarget'] = isset($params['orderTarget']) ? !$params['orderTarget'] : true;
        return \Arr::query($params);
    }
}
