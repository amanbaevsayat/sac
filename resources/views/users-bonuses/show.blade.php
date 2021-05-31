@extends('adminlte::page')

@section('content')
@php
$data = [
    'from' => request()->get('from') ?? null,
    'to' => request()->get('to') ?? null,
    'productId' => request()->get('productId') ?? null,
    'period' => request()->get('period') ?? null,
    'currentPoint' => request()->get('currentPoint') ?? null,
    'lastPoint' => request()->get('lastPoint') ?? null,
    'userId' => request()->get('userId') ?? null,
];

@endphp
<users-bonuses-component 
    route-prop="{{ route(\Request::route()->getName()) }}"
    :products-prop="{{ json_encode($products) }}"
    :periods-prop="{{ json_encode(\App\Models\Bonus::PERIODS) }}"
    :data-prop="{{ json_encode($data) }}"
    :chart-prop="{{ json_encode($chart) }}"
    :users-bonuses-prop="{{ json_encode($usersBonuses) }}"
    :bonuses-headers-prop="{{ json_encode(\App\Models\Bonus::HEADERS) }}"
    :total-sum-prop="{{ json_encode($usersBonusesForChart) }}"
    :records-prop="{{ json_encode($recordsBonuses) }}"
    :users-bonuses-group-unix-date-prop="{{ json_encode($usersBonusesGroupByUnixDate) }}"
></users-bonuses-component>
@stop