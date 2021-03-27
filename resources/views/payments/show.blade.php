@extends('adminlte::page')

@section('title', 'Просмотр платежа')

@section('content_header')
<h1>Просмотр платежа</h1>
@stop

@section('content')
<div class="row">
    <div class="col-6">
        <div class="card mb-2">
            <div class="card-header">
                <form id="deletesubscriptionForm" action="{{ route('payments.destroy', [$payment->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <h5>
                        {{$payment->id}}
                        <small class="float-right">
                            <a href="#" id="deletesubscriptionFormButton" class="btn btn-danger btn-sm">Удалить</a>
                        </small>
                    </h5>
                </form>
            </div>
            <div class="card-body">
                <a href="{{ route('payments.edit', [$payment->id]) }}" class="btn btn-warning btn-sm float-right">Изменить</a>
                Клиент:
                @if (isset($payment->customer_id))
                <a target="_blank" href="{{ route('customers.index', ['id' => $payment->customer_id]) }}">{{ $payment->customer->name ?? null }}</a>
                @endif
                <br>
                @if (isset($payment->customer))
                Телефон: {{ $payment->customer->phone }}
                @endif
                <br>
                Абонемент:
                @if (isset($payment->subscription_id))
                <a target="_blank" href="{{ route('subscriptions.index', ['id' => $payment->subscription_id]) }}">{{ $payment->subscription_id ?? null }}</a>
                @endif
                <br>
                Карта клиента: {{ $payment->card_id }} <br>
                Оператор:
                @if (isset($payment->user_id))
                <a target="_blank" href="{{ route('users.show', [$payment->user_id]) }}">{{ $payment->user->account ?? null }}</a>
                @endif
                <br>
                Тип платежа: {{ $payment->type }} <br>
                @if ($payment->type == 'cloudpayments')
                Платежная страница:
                @if (isset($payment->subscription_id))
                <a target="_blank" href="{{ route('cloudpayments.show_widget', [$payment->subscription_id]) }}">Ссылка</a>
                @endif
                <br>
                TransactionId: {{ $payment->transaction_id }} <br>
                SubscriptionId: {{ $payment->subscription->cp_subscription_id }} <br>
                @endif
                Сумма: {{ $payment->amount }} <br>
                Количество: {{ $payment->quantity }} <br>
                PaidedAt: {{ $payment->paided_at }} <br>
                Статус платежа: {{ \App\Models\Payment::STATUSES[$payment->status] ?? $payment->status ?? null }} ({{ $payment->status }})<br>
                {{-- Рекуррент: {{ $payment->recurrent }} <br> --}}
                {{-- Дата старта: {{ $payment->start_date }} <br> --}}
                {{-- Интервал: {{ $payment->interval }} <br> --}}
                {{-- Период: {{ $payment->period }} <br> --}}
            </div>
            <div class="card-footer">
                <a href="{{ route('payments.index') }}">К списку</a>
            </div>
        </div>
    </div>
    @foreach($payment->data ?? [] as $key=>$item)
    <div class="col-6">
        <div class="card mb-2">
            <div class="card-header">
                <h5>
                    {{$key}}
                </h5>
            </div>
            <div class="card-body">
                <pre>
                {{ print_r($item) }}
                </pre>
            </div>
            <div class="card-footer">
                <a href="{{ route('payments.index') }}">К списку</a>
            </div>
        </div>
    </div>
    @endforeach
</div>

@stop

@section('css')
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#deletesubscriptionFormButton').on('click', function(e) {
            if (confirm('Вы действительно хотите удалить?')) {
                $("#deletesubscriptionForm").submit();
            }
        });
    });
</script>
<script>
    '@if(session()->has("success"))'
    $(document).Toasts('create', {
        title: 'Успешно.',
        body: '{{ session()->get("success") }}',
        autohide: true,
        delay: 5000
    });
    @endif
</script>
@stop