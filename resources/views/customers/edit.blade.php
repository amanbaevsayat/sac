@extends('adminlte::page')

@section('title', 'Изменить данные клиента')

@section('content_header')
<h1>Изменить данные клиента</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('customers.update', [$customer->id]) }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('PATCH') }}
            <div class="form-group row">
                <label for="name" class="col-sm-2 col-form-label">Имя</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="name" value="{{ $customer->name }}" name="name">
                </div>
            </div>
            <div class="form-group row">
                <label for="phone" class="col-sm-2 col-form-label">Телефон</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="phone" value="{{ $customer->phone }}" name="phone">
                </div>
            </div>
            <div class="form-group row">
                <label for="email" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" id="email" value="{{ $customer->email }}" name="email">
                </div>
            </div>
            <div class="form-group row">
                <label for="comments" class="col-sm-2 col-form-label">Комментарий</label>
                <div class="col-sm-10">
                    <textarea type="comments" class="form-control" id="comments" value="" name="comments">{{ $customer->comments }}</textarea>
                </div>
            </div>
            

            


            <div class="form-group">
                <input type="submit" value="Сохранить" class="btn btn-success" />
            </div>
        </form>
        <a href="{{ route('customers.index') }}">К списку</a>
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
