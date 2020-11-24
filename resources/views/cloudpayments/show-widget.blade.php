@extends('adminlte::master')


@section('body')
<div id="app">
    <cloudpayments-widget 
        :payment-prop="{{ json_encode($payment) }}" 
        :customer-prop="{{ json_encode($customer) }}" 
        :subscription-prop="{{ json_encode($subscription) }}"
        :product-prop="{{ json_encode($product) }}"
        :price-prop="{{ json_encode($price) }}"
        public-id-prop="{{ $publicId }}"
    ></cloudpayments-widget>
</div>
@endsection
