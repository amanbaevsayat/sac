@extends('adminlte::page')

@section('title', 'Просмотр продукта')

@section('content_header')
<h1>Просмотр продукта</h1>
@stop

@section('content')
<div class="row">
    <div class="col-6">
        <div class="card mb-2">
            <div class="card-header">
                <form id="deleteProductForm" action="{{ route('products.destroy', [$product->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <h5>
                        {{$product->title}}
                        <small class="float-right">
                            <a href="#" id="deleteProductFormButton" class="btn btn-danger btn-sm">Удалить</a>
                        </small>
                    </h5>
                </form>
            </div>
            <div class="card-body">
                <a href="{{ route('products.edit', [$product->id]) }}" class="btn btn-warning btn-sm float-right">Изменить</a>

                Код: {{ $product->code}} <br>
                Описание: {{ $product->description }} <br>
                Цены: <br>
                @foreach($product->prices as $price)
                    {{ $price->price }} тг <br>
                @endforeach
            </div>
            <div class="card-footer">
                <a href="{{ route('products.index') }}">К списку</a>
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
        $('#deleteProductFormButton').on('click', function(e) {
            if (confirm('Вы действительно хотите удалить?')) {
                $("#deleteProductForm").submit();
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
