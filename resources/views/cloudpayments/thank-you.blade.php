@extends('adminlte::master')


@section('body')
<div id="app">
    <cloudpayments-thank-you 
        :subscription-prop="{{ json_encode($subscription) }}"
    ></cloudpayments-thank-you>
</div>
@endsection
