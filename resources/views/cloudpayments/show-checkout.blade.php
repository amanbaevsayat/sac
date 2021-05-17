@extends('adminlte::master')


@section('body')
<div id="app">
    <cloudpayments-checkout 
        :payment-prop="{{ json_encode($payment) }}" 
        :customer-prop="{{ json_encode($customer) }}" 
        :subscription-prop="{{ json_encode($subscription) }}"
        :product-prop="{{ json_encode($product) }}"
        public-id-prop="{{ $publicId }}"
    ></cloudpayments-checkout>
</div>
@endsection
