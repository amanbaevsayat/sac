@extends('adminlte::page')

@section('title', 'Создать команду')

@section('content_header')
<h1>Создать команду</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('teams.store') }}" method="POST">
            @csrf
            <div class="form-group row">
                <label for="name" class="col-sm-2 col-form-label">Название</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="name" value="{{ old('name') }}" name="name">
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label">Название для операторов</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" value="{{ old('title') }}" name="title">
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label for="prices" class="col-sm-2 col-form-label">Операторы</label>
                <div class="col-sm-10">
                    <product-users-component :product-users-prop="{{ json_encode(old('teamUsers') ?? []) }}" :users-prop="{{ json_encode($users) }}"></product-users-component>
                </div>
            </div>
            <div class="form-group">
                <input type="submit" value="Сохранить" class="btn btn-success" />
            </div>
        </form>
        <a href="{{ route('teams.index') }}">К списку</a>
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
