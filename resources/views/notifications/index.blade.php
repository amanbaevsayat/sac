@extends('adminlte::page')

@section('title', 'Уведомления')

@section('content')
<div class="table-responsive bg-white">
    <h2>{{ App\Models\Notification::TYPES[request()->get('type')] ?? '' }}</h2>
    <h4>Данные обновляются каждые 10 минут</h4>
    <index-component 
        prefix-prop="notifications"
        create-link-prop="{{ route('notifications.create') }}"
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
