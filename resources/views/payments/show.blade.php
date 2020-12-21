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
                Клиент: {{ $payment->customer_id }} <br>
                Абонемент: {{ $payment->subscription_id }} <br>
                Карта клиента: {{ $payment->card_id }} <br>
                Тип платежа: {{ $payment->type }} <br>
                Slug: {{ $payment->slug }} <br>
                Количество: {{ $payment->quantity }} <br>
                Статус платежа: {{ $payment->status }} <br>
                Сумма: {{ $payment->amount }} <br>
                Рекуррент: {{ $payment->recurrent }} <br>
                Дата старта: {{ $payment->start_date }} <br>
                Интервал: {{ $payment->interval }} <br>
                Период: {{ $payment->period }} <br>
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
    @if(session()->has('success'))
        $(document).Toasts('create', {
            title: 'Успешно.',
            body: '{{ session()->get("success") }}',
            autohide: true,
            delay: 5000
        });
    @endif
</script>
@stop
