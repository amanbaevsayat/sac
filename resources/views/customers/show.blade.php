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
                <form id="deleteCustomerForm" action="{{ route('customers.destroy', [$customer->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <h5>
                        {{$customer->name}}
                        <small class="float-right">
                            <a href="#" id="deleteCustomerFormButton" class="btn btn-danger btn-sm">Удалить</a>
                        </small>
                    </h5>
                </form>
            </div>
            <div class="card-body">
                <a href="{{ route('customers.edit', [$customer->id]) }}" class="btn btn-warning btn-sm float-right">Изменить</a>

                Телефон: {{$customer->phone}} <br>
                Email: {{$customer->email}} <br>
                @if (isset($customer->remark))
                Метка: {{$customer->remark->title}} <br>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('customers.index') }}">К списку</a>
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
        $('#deleteCustomerFormButton').on('click', function(e) {
            if (confirm('Вы действительно хотите удалить?')) {
                $("#deleteCustomerForm").submit();
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
