@extends('adminlte::page')

@section('title', 'Создать клиента')

@section('content_header')
<h1>Создать клиента</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('customers.store') }}" method="POST">
            @csrf
            <div class="form-group row">
                <label for="name" class="col-sm-2 col-form-label">Имя</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="name" value="{{ old('name') }}" name="name">
                </div>
            </div>
            <div class="form-group row">
                <label for="phone" class="col-sm-2 col-form-label">Телефон</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="phone" value="{{ old('phone') }}" name="phone">
                </div>
            </div>
            <div class="form-group row">
                <label for="email" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" id="email" value="{{ old('email') }}" name="email">
                </div>
            </div>
            <div class="form-group row">
                <label for="comments" class="col-sm-2 col-form-label">Комментарий</label>
                <div class="col-sm-10">
                    <textarea type="comments" class="form-control" id="comments" value="" name="comments">{{ old('comments') }}</textarea>
                </div>
            </div>
            <div class="form-group row">
                <label for="remark_id" class="col-sm-2 col-form-label">Ремарка</label>
                <div class="col-sm-10">
                    <select name="remark_id" id="remark_id" class="form-control" name="remark_id">
                        @foreach($remarks as $remark)
                        <option value="{{$remark->id}}">
                            {{$remark->title}}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>


            <div class="form-group">
                <input type="submit" value="Добавить" class="btn btn-success" />
            </div>
        </form>
        <a href="{{ route('customers.index') }}">К списку</a>
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
