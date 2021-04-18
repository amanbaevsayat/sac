@extends('adminlte::page')

@section('title', 'Логи юзеров')

{{--@section('content_header')
<h1>Абонементы</h1>
@stop--}}

@section('content')
<div class="table-responsive bg-white">
    <index-component 
        prefix-prop="userlogs"
        create-link-prop="{{ route('userlogs.create') }}"
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
