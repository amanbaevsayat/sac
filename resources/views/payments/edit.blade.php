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
