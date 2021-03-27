@extends('adminlte::page')

@section('content')
@php
$data = [
'from' => request()->get('from') ?? null,
'to' => request()->get('to') ?? null,
'productId' => request()->get('productId') ?? null,
];
@endphp
<statistics-component 
    route-prop="{{ route(\Request::route()->getName()) }}"
    :products-prop="{{ json_encode($products) }}"
    :charts-prop="{{ json_encode($chats) }}"
    :data-prop="{{ json_encode($data) }}"
></statistics-component>
@stop