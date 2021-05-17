@extends('adminlte::page')

@section('content')
@php
$data = [
    'from' => request()->get('from') ?? null,
    'to' => request()->get('to') ?? null,
    'productId' => request()->get('productId') ?? null,
    'period' => request()->get('period') ?? null,
];

@endphp
<users-bonuses-component 
    route-prop="{{ route(\Request::route()->getName()) }}"
    :products-prop="{{ json_encode($products) }}"
    :periods-prop="{{ json_encode(\App\Models\UsersBonuses::PERIODS) }}"
    :data-prop="{{ json_encode($data) }}"
    :chart-prop="{{ json_encode($chart) }}"
    :users-bonuses-prop="{{ json_encode($usersBonuses) }}"
    :bonuses-headers-prop="{{ json_encode(\App\Models\UsersBonuses::HEADERS) }}"
    :current-period-total-prop="{{ $currentPeriodTotal }}"
    :product-users-prop="{{ json_encode($productUsers) }}"
></users-bonuses-component>
@stop