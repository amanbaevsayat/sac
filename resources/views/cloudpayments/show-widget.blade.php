@extends('adminlte::master')


@section('body')
<div id="app">
    <cloudpayments-widget 
        :payment-prop="{{ json_encode($payment) }}" 
        :customer-prop="{{ json_encode($payment->customer) }}" 
        :subscription-prop="{{ json_encode($payment->subscription) }}"
        :product-prop="{{ json_encode($payment->subscription->product) }}"
        public-id-prop="{{ $publicId }}"
    ></cloudpayments-widget>
</div>
@endsection
