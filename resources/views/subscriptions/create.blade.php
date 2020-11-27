@extends('adminlte::page')

@section('title', 'Создать абонемент')

@section('content_header')
<h1>Создать абонемент</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('subscriptions.store') }}" method="POST">
            @csrf
            <div class="form-group row">
                <label for="customer_id" class="col-sm-2 col-form-label">Выбрать клиента</label>
                <div class="col-sm-10">
                    <select name="customer_id" id="customer_id" class="form-control selectpicker" name="customer_id" data-show-subtext="true" data-live-search="true">
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" data-subtext="{{ $customer->phone }}" {{ $customer->id == old('customer_id') ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="products" class="col-sm-2 col-form-label">Выбрать продукт</label>
                <div class="col-sm-10">
                    <select id="product_id" class="form-control selectpicker" name="product_id"  data-show-subtext="true" data-live-search="true">
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" data-subtext="{{ $product->price }}" {{ $product->id == old('product_id') ? 'selected' : '' }}>
                            {{ $product->title }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="description" class="col-sm-2 col-form-label">Описание абонемента</label>
                <div class="col-sm-10">
                    <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="form-group row">
                <label for="amount" class="col-sm-2 col-form-label">Цена</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="amount" value="{{ old('amount') }}" name="amount">
                </div>
            </div>
            @php
                $now = date("Y-m-d H:i:s");
                $startedAt = Carbon\Carbon::parse(old('started_at') ?? $now)->toW3cString();
                $pausedAt = null;
                $endedAt = null;
            @endphp
            <div class="form-group row">
                <label for="started_at" class="col-sm-2 col-form-label">Дата старта</label>
                <div class="col-sm-10">
                    <datetime
                        name="started_at"
                        value="{{ $startedAt }}"
                        type="date"
                        input-class="form-control"
                        valueZone="Asia/Almaty"
                        zone="Asia/Almaty"
                        format="DDD"
                    ></datetime>
                </div>
            </div>
            <div class="form-group row">
                <label for="paused_at" class="col-sm-2 col-form-label">Дата заморозки</label>
                <div class="col-sm-10">
                    <datetime
                        name="paused_at"
                        value="{{ $pausedAt }}"
                        type="date"
                        input-class="form-control"
                        valueZone="Asia/Almaty"
                        zone="Asia/Almaty"
                        format="DDD"
                    ></datetime>
                </div>
            </div>
            <div class="form-group row">
                <label for="ended_at" class="col-sm-2 col-form-label">Дата окончания</label>
                <div class="col-sm-10">
                    <datetime
                        name="ended_at"
                        value="{{ $endedAt }}"
                        type="date"
                        input-class="form-control"
                        valueZone="Asia/Almaty"
                        zone="Asia/Almaty"
                        format="DDD"
                    ></datetime>
                </div>
            </div>

            
            <div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label">Статус</label>
                <div class="col-sm-10">
                    <select id="status" class="form-control selectpicker" name="status">
                        @foreach(\App\Models\Subscription::STATUSES as $key=>$title)
                        <option value="{{ $key }}" {{ $key == old('status') ? 'selected' : '' }}>
                            {{ $title }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            



            <div class="form-group">
                <input type="submit" value="Добавить" class="btn btn-success" />
            </div>
        </form>
        <a href="{{ route('subscriptions.index') }}">К списку</a>
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
