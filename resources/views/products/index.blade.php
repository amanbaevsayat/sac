@extends('adminlte::page')

@section('title', 'Услуги')

@section('content')
<div class="table-responsive bg-white">
    <index-component 
        prefix-prop="products"
        create-link-prop="{{ route('products.create') }}"
    ></index-component>
</div>
@stop

@section('css')
@stop

@section('js')
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
