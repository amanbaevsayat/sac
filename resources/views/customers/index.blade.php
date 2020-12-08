@extends('adminlte::page')

@section('title', 'Клиенты')

@section('content')
<div class="table-responsive bg-white">
    <index-component 
        prefix-prop="customers"
        create-link-prop="{{ route('customers.create') }}"
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
