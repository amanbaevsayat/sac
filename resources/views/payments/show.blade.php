@extends('adminlte::page')

@section('title', 'Просмотр клиента')

@section('content_header')
<h1>Просмотр клиента</h1>
@stop

@section('content')
<div class="row">
    <div class="col-6">
        <div class="card mb-2">
            <div class="card-header">
                <form id="deletesubscriptionForm" action="{{ route('subscriptions.destroy', [$customer->id, $subscription->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <h5>
                        {{$subscription->name}}
                        <small class="float-right">
                            <a href="#" id="deletesubscriptionFormButton" class="btn btn-danger btn-sm">Удалить</a>
                        </small>
                    </h5>
                </form>
            </div>
            <div class="card-body">
                <a href="{{ route('subscriptions.edit', [$subscription->id]) }}" class="btn btn-warning btn-sm float-right">Изменить</a>

                Телефон: {{$subscription->phone}} <br>
                Email: {{$subscription->email}} <br>
                @if (isset($subscription->remark))
                Метка: {{$subscription->remark->title}} <br>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('subscriptions.index') }}">К списку</a>
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
        $('#deletesubscriptionFormButton').on('click', function(e) {
            if (confirm('Вы действительно хотите удалить?')) {
                $("#deletesubscriptionForm").submit();
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
