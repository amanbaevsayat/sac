@extends('adminlte::page')

@section('title', 'Создать платеж')

@section('content_header')
<h1>Создать платеж</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('payments.store') }}" method="POST">
            @csrf
            <div class="form-group row">
                <label for="customer_id" class="col-sm-2 col-form-label">Выбрать клиента</label>
                <div class="col-sm-10">
                    <select name="customer_id" id="customer_id" class="form-control selectpicker" name="customer_id" data-show-subtext="true" data-live-search="true">
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" data-subtext="{{ $customer->phone }}" {{ $customer->id == old('customer_id') ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="subscription_id" class="col-sm-2 col-form-label">Выбрать подписку</label>
                <div class="col-sm-10">
                    <select id="subscription_id" class="form-control selectpicker" name="subscription_id"  data-show-subtext="true" data-live-search="true">
                        @foreach($customer->subscriptions as $subscription)
                        <option value="{{ $subscription->id }}" data-subtext="{{ "({$subscription->started_at})-({$subscription->ended_at})"}}" {{ $subscription->id == old('subscription_id') ? 'selected' : '' }}>
                            {{ $subscription->product->title }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="type" class="col-sm-2 col-form-label">Тип оплаты</label>
                <div class="col-sm-10">
                    <select id="type" class="form-control selectpicker" name="type">
                        @foreach(\App\Models\Subscription::PAYMENT_TYPE as $key=>$value)
                        <option value="{{ $key }}" {{ $key == old('type') ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <payment-show-recurrent :data-prop="{{ json_encode([
                'recurrent' => old('recurrent'),
                'start_date' => old('start_date') ? Carbon\Carbon::parse(old('start_date'))->toW3cString() : null,
                'interval' => old('interval'),
                'period' => old('period'),
            ]) }}"></payment-show-recurrent>

            <div class="form-group row">
                <label for="period" class="col-sm-2 col-form-label">Добавить файл</label>
                <div class="col-sm-10">
                    <upload-file name-prop="data" value-prop="{{ old('data') }}"></upload-file>
                </div>
            </div>
            <div class="form-group">
                <input type="submit" value="Добавить" class="btn btn-success" />
            </div>
        </form>
        <a href="{{ route('payments.index') }}">К списку</a>
    </div>
</div>
@stop

@section('css')
@stop

@section('js')
<script>
    @if($errors->any())
        @foreach($errors->all() as $key => $error)
        $(document).Toasts('create', {
            title: 'Ошибка.',
            body: '{{ $error }}',
            autohide: true,
            delay: 5000
        });
        @endforeach
    @endif
</script>
@stop
