@extends('adminlte::page')

@section('title', 'Клиенты')

@section('content_header')
<h1>Клиенты</h1>
@stop

@section('content')
<div class="row">
    <div class="col-1">
        <a href="{{ route('customers.create') }}" class="btn btn-info btn-block text-white mb-2" title="Добавить">
            <i class="fa fa-plus"></i>
        </a>
    </div>
</div>
<div class="table-responsive bg-white">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Имя</th>
                <th scope="col">Телефон</th>
                <th scope="col">Email</th>
                <th scope="col">Комментарий</th>
                <th scope="col">
                    <a href="{{ route('customers.index') }}?{{$sortService->sortable('remark_id', request()->query())}}">
                        Метка
                    </a>
                </th>
                <th scope="col">
                    <i class="fa fa-cog"></i>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $key => $customer)
            <tr data-id="{{ $customer->id }}">
                <th scope="row">{{ ($customers->currentpage()-1) * $customers->perpage() + $key + 1  }}</th>
                <td class="editable">
                    <input type="text" name="name" class="form-control form-control-sm" value="{{ $customer->name }}" readonly />
                </td>
                <td class="editable">
                    <input type="text" name="phone" class="form-control form-control-sm" value="{{ $customer->phone }}" readonly />
                </td>
                <td class="editable">
                    <input type="text" name="email" class="form-control form-control-sm" value="{{ $customer->email }}" readonly />
                </td>
                <td class="editable">
                    <textarea type="text" name="comments" class="form-control form-control-sm" readonly>{{ $customer->comments }}</textarea>
                </td>
                <td class="editable">
                    <select name="remark_id" class="form-control form-control-sm" style="background-color: {{$customer->remark->color }};">
                        @foreach($remarks as $remark)
                        <option value="{{ $remark->id }}" data-background-color="{{ $remark->color }};" style="background-color: {{ $remark->color }};" @if($customer->remark->id == $remark->id)
                            selected
                            @endif
                            >
                            {{ $remark->title }}
                        </option>
                        @endforeach
                    </select>
                </td>
                <td class="text-right">
                    <button type="button" class="btn btn-danger btn-sm save-button" style="display: none;" title="Сохранить">
                        <i class="fa fa-save"></i>
                    </button>
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-cog"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="{{ route('customers.edit', [$customer->id]) }}" class="dropdown-item" title="Редактировать">
                                Редактировать
                            </a>
                            <a href="{{ route('customers.show', [$customer->id]) }}" class="dropdown-item" title="Подробнее">
                                Подробнее
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $customers->withQueryString()->links() }}
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