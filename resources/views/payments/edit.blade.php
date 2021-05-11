@extends('adminlte::page')

@section('title', 'Изменить данные платежа')

@section('content_header')
<h1>Изменить данные платежа</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('payments.update', [$payment->id]) }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('PATCH') }}
            <div class="form-group row">
                <label for="amount" class="col-sm-2 col-form-label">Цена</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" id="amount" value="{{ $payment->amount }}" name="amount">
                </div>
            </div>
            <div class="form-group row">
                <label for="from" class="col-sm-2 col-form-label">С</label>
                <div class="col-sm-10">
                    <date-component name-prop="from" value-prop="{{ isset($payment->data['subscription']['from']) ? date(DATE_ATOM, strtotime($payment->data['subscription']['from'])) : null }}"></date-component>
                </div>
            </div>

            <div class="form-group row">
                <label for="to" class="col-sm-2 col-form-label">До</label>
                <div class="col-sm-10">
                    <date-component name-prop="to" value-prop="{{ isset($payment->data['subscription']['to']) ? date(DATE_ATOM, strtotime($payment->data['subscription']['to'])) : null }}"></date-component>
                </div>
            </div>
            
            <div class="form-group row">
                <label for="quantity" class="col-sm-2 col-form-label">Количество</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="quantity" value="{{ $payment->quantity }}" name="quantity">
                </div>
            </div>
            @if ($payment->type == 'transfer')
            <div class="form-group row">
                <label for="file" class="col-sm-2 col-form-label">Чек</label>
                <div class="col-sm-10">
                    <upload-file name-prop="file" value-prop="{{ $payment->data['check'] ?? null }}"></upload-file>
                </div>
            </div>
            @endif
            <div class="form-group">
                <input type="submit" value="Сохранить" class="btn btn-success" />
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
