@extends('adminlte::page')

@section('title', 'Изменить данные пользователя')

@section('content_header')
<h1>Изменить данные пользователя</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('users.update', [$user->id]) }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('PATCH') }}
            <div class="form-group row">
                <label for="name" class="col-sm-2 col-form-label">Имя</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="name" value="{{ $user->name }}" name="name">
                </div>
            </div>
            <div class="form-group row">
                <label for="account" class="col-sm-2 col-form-label">Аккаунт</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="account" value="{{ $user->account }}" name="account">
                </div>
            </div>
            <div class="form-group row">
                <label for="email" class="col-sm-2 col-form-label">E-mail</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="email" value="{{ $user->email }}" name="email">
                </div>
            </div>
            <div class="form-group row">
                <label for="phone" class="col-sm-2 col-form-label">Телефон</label>
                <div class="col-sm-10">
                    <phone-component name-prop="phone" value-prop="{{ $user->phone }}" class-prop="form-control">
                </div>
            </div>
            <div class="form-group row">
                <label for="prices" class="col-sm-2 col-form-label">Роль</label>
                <div class="col-sm-10">
                    <select id="role_id" class="form-control selectpicker" name="role_id">
                        @foreach(\App\Models\Role::pluck('title', 'id') as $key=>$value)
                        <option value="{{ $key }}" {{ $key == $user->role_id ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="pass" class="col-sm-2 col-form-label">Пароль</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="pass" value="{{ $user->pass }}" name="pass">
                </div>
            </div>

            <div class="form-group">
                <input type="submit" value="Сохранить" class="btn btn-success" />
            </div>
        </form>
        <a href="{{ route('users.index') }}">К списку</a>
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
