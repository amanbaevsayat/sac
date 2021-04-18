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
<statistics-component 
    route-prop="{{ route(\Request::route()->getName()) }}"
    :products-prop="{{ json_encode($products) }}"
    :periods-prop="{{ json_encode(\App\Models\StatisticsModel::PERIODS) }}"
    :charts-prop="{{ json_encode($chats) }}"
    :data-prop="{{ json_encode($data) }}"
></statistics-component>
@stop