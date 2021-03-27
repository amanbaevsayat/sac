@extends('adminlte::page')

@section('content')
@php
$statistics = [
'from' => old('from') ?? null,
'to' => old('to') ?? null,
'leads' => old('leads') ?? null,
'trial' => old('trial') ?? null,
'customers' => old('customers') ?? null,
'advertising_costs' => old('advertising_costs') ?? null,
'bonus_costs' => old('bonus_costs') ?? null,
'shooting_costs' => old('shooting_costs') ?? null,
'coach_costs' => old('coach_costs') ?? null,
'total_payments' => old('total_payments') ?? null,
];
@endphp
<statistics-component :statistics-prop="{{ json_encode($statistics) }}"></statistics-component>
@stop