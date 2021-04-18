@extends('adminlte::page')

@section('title', 'Изменить данные продукта')

@section('content_header')
<h1>Изменить данные продукта</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('products.update', [$product->id]) }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('PATCH') }}
            <div class="form-group row">
                <label for="code" class="col-sm-2 col-form-label">Код</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="code" value="{{ $product->code }}" name="code">
                </div>
            </div>
            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label">Название</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" value="{{ $product->title }}" name="title">
                </div>
            </div>
            <div class="form-group row">
                <label for="description" class="col-sm-2 col-form-label">Описание</label>
                <div class="col-sm-10">
                    <textarea class="form-control" id="description" value="" name="description">{{ $product->description }}</textarea>
                </div>
            </div>
            <div class="form-group row">
                <label for="prices" class="col-sm-2 col-form-label">Доступные цены</label>
                <div class="col-sm-10">
                    <product-price-component :prices-prop="{{ json_encode($productPrices) }}"></product-price-component>
                </div>
            </div>

            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label">Имя тренера</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" value="{{ $product->data['name'] ?? null }}" name="data[name]">
                </div>
            </div>

            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label">Описание тренера</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" value="{{ $product->data['position'] ?? null }}" name="data[position]">
                </div>
            </div>

            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label">Телефон</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" value="{{ $product->data['phone'] ?? null }}" name="data[phone]">
                </div>
            </div>

            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label">Instagram (account)</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" value="{{ $product->data['instagram'] ?? null }}" name="data[instagram]">
                </div>
            </div>

            <div class="form-group row">
                <label for="period" class="col-sm-2 col-form-label">Добавить картинку</label>
                <div class="col-sm-10">
                    <upload-file name-prop="data[image]" value-prop="{{ $product->data['image'] ?? null }}"></upload-file>
                </div>
            </div>

            <div class="form-group">
                <input type="submit" value="Сохранить" class="btn btn-success" />
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
