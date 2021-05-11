@extends('adminlte::page')

@section('title', 'Создать пользователя')

@section('content_header')
<h1>Создать пользователя</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="form-group row">
                <label for="account" class="col-sm-2 col-form-label">Аккаунт</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="account" value="{{ old('account') }}" name="account">
                </div>
            </div>
            <div class="form-group row">
                <label for="email" class="col-sm-2 col-form-label">E-mail</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="email" value="{{ old('email') }}" name="email">
                </div>
            </div>
            <div class="form-group row">
                <label for="phone" class="col-sm-2 col-form-label">Телефон</label>
                <div class="col-sm-10">
                    <phone-component name-prop="phone" value-prop="{{ old('phone', null) }}" class-prop="form-control">
                </div>
            </div>
            <div class="form-group row">
                <label for="prices" class="col-sm-2 col-form-label">Роль</label>
                <div class="col-sm-10">
                    <select id="role_id" class="form-control selectpicker" name="role_id">
                        @foreach(\App\Models\Role::pluck('title', 'id') as $key=>$value)
                        <option value="{{ $key }}" {{ $key == old('role_id') ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="pass" class="col-sm-2 col-form-label">Пароль</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="pass" value="{{ old('pass') }}" name="pass">
                </div>
            </div>
            <div class="form-group">
                <input type="submit" value="Добавить" class="btn btn-success" />
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
</script>
@stop
