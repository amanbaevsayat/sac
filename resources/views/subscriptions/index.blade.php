@extends('adminlte::page')

@section('title', 'Подписки')

@section('content_header')
<h1>Подписки</h1>
@stop

@section('content')
<div class="row">
    <div class="col-1">
        <a href="{{ route('subscriptions.create') }}" class="btn btn-info btn-block text-white mb-2" title="Создать подписку">
            <i class="fa fa-plus"></i>
        </a>
    </div>
</div>
<div class="table-responsive bg-white">
    <index-component prefix-prop="subscriptions"></index-component>
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
