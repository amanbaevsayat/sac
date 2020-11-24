@extends('adminlte::page')

@section('title', 'Просмотр абонемента')

@section('content_header')
<h1>Просмотр абонемента</h1>
@stop

@section('content')
<div class="row">
    <div class="col-6">
        <div class="card mb-2">
            <div class="card-header">
                <form id="deletesubscriptionForm" action="{{ route('subscriptions.destroy', [$subscription->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <h5>
                        {{$subscription->customer->name }} - {{ $subscription->product->title }}
                        <small class="float-right">
                            <a href="#" id="deletesubscriptionFormButton" class="btn btn-danger btn-sm">Удалить</a>
                        </small>
                    </h5>
                </form>
            </div>
            <div class="card-body">
                <a href="{{ route('subscriptions.edit', [$subscription->id]) }}" class="btn btn-warning btn-sm float-right">Изменить</a>

                Описание подписки: {{ $subscription->description }} <br>
                Цена: {{ $subscription->amount }} <br>
                Дата старта: {{ $subscription->started_at }} <br>
                Дата заморозки: {{ $subscription->paused_at }} <br>
                Дата окончания: {{ $subscription->ended_at }} <br>
                Статус: {{ $subscription->status }} <br>
            </div>
            <div class="card-footer">
                <a href="{{ route('subscriptions.index') }}">К списку</a>
            </div>
        </div>
    </div>
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
