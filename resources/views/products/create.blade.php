@extends('adminlte::page')

@section('title', 'Создать продукт')

@section('content_header')
<h1>Создать продукт</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <div class="form-group row">
                <label for="code" class="col-sm-2 col-form-label">Код</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="code" value="{{ old('code') }}" name="code">
                </div>
            </div>
            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label">Заголовок</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" value="{{ old('title') }}" name="title">
                </div>
            </div>
            <div class="form-group row">
                <label for="description" class="col-sm-2 col-form-label">Описание</label>
                <div class="col-sm-10">
                    <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
                </div>
            </div>
            <div class="form-group row">
                <label for="price" class="col-sm-2 col-form-label">Цена</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="price" value="{{ old('price') }}" name="price">
                </div>
            </div>
            <div class="form-group row">
                <label for="trial_price" class="col-sm-2 col-form-label">Пробная цена</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="trial_price" value="{{ old('trial_price') }}" name="trial_price">
                </div>
            </div>
            <div class="form-group">
                <input type="submit" value="Добавить" class="btn btn-success" />
            </div>
        </form>
        <a href="{{ route('products.index') }}">К списку</a>
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
