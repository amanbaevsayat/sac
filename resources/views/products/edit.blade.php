@extends('adminlte::page')

@section('title', 'Изменить данные продукта')

@section('content_header')
<h1>Изменить данные продукта</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('products.update', [$product->id]) }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('PATCH') }}
            <div class="form-group row">
                <label for="code" class="col-sm-2 col-form-label">Код</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="code" value="{{ $product->code }}" name="code">
                </div>
            </div>
            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label">Название</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" value="{{ $product->title }}" name="title">
                </div>
            </div>
            <div class="form-group row">
                <label for="description" class="col-sm-2 col-form-label">Описание</label>
                <div class="col-sm-10">
                    <textarea class="form-control" id="description" value="" name="description">{{ $product->description }}</textarea>
                </div>
            </div>
            <div class="form-group row">
                <label for="prices" class="col-sm-2 col-form-label">Доступные цены</label>
                <div class="col-sm-10">
                    <product-price-component :prices-prop="{{ json_encode($productPrices) }}"></product-price-component>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label for="prices" class="col-sm-2 col-form-label">Доступные типы оплат</label>
                <div class="col-sm-10">
                    <product-payment-type-component :product-payment-types-prop="{{ json_encode($productPaymentTypes) }}" :payment-types-prop="{{ json_encode($paymentTypes) }}"></product-payment-type-component>
                </div>
            </div>
            {{--<hr>
            <div class="form-group row">
                <label for="prices" class="col-sm-2 col-form-label">Операторы услуги</label>
                <div class="col-sm-10">
                    <product-users-component :product-users-prop="{{ json_encode($productUsers) }}" :users-prop="{{ json_encode($users) }}"></product-users-component>
                </div>
            </div>--}}

            <hr>
            <div class="form-group row">
                <label for="prices" class="col-sm-2 col-form-label">Команда продукта</label>
                <div class="col-sm-10">
                    <product-teams-component :product-teams-prop="{{ json_encode($productTeams) }}" :teams-prop="{{ json_encode($teams) }}"></product-teams-component>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label for="prices" class="col-sm-2 col-form-label">Диаграммы продукта</label>
                <div class="col-sm-10">
                    <product-charts-component :product-charts-prop="{{ json_encode($productCharts) }}" :charts-prop="{{ json_encode($charts) }}"></product-teams-component>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label for="prices" class="col-sm-2 col-form-label">Причины отказов</label>
                <div class="col-sm-10">
                    <product-reasons-component :reasons-prop="{{ json_encode($reasons) }}"></product-reasons-component>
                </div>
            </div>
            <hr>
            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label">Имя тренера</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" value="{{ $product->data['name'] ?? null }}" name="data[name]">
                </div>
            </div>

            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label">Описание тренера</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" value="{{ $product->data['position'] ?? null }}" name="data[position]">
                </div>
            </div>

            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label">Заголовок телефона</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" value="{{ $product->data['phone_title'] ?? null }}" name="data[phone_title]">
                </div>
            </div>

            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label">Телефон</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" value="{{ $product->data['phone'] ?? null }}" name="data[phone]">
                </div>
            </div>

            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label">Заголовок Instagram-а</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" value="{{ $product->data['instagram_title'] ?? null }}" name="data[instagram_title]">
                </div>
            </div>

            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label">Instagram (account)</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" value="{{ $product->data['instagram'] ?? null }}" name="data[instagram]">
                </div>
            </div>

            <div class="form-group row">
                <label for="period" class="col-sm-2 col-form-label">Добавить картинку</label>
                <div class="col-sm-10">
                    <div style="max-width: 300px">
                        <upload-file name-prop="data[image]" value-prop="{{ $product->data['image'] ?? null }}"></upload-file>
                    </div>
                </div>
            </div>

            <hr>
            <div class="form-group row">
                <label for="period" class="col-sm-2 col-form-label">Thank you page</label>
                <div class="col-sm-10">
                    <div style="margin-bottom: 15px">
                        <a class="btn btn-success" href="{{ route('cloudpayments.thank_you', [$product->id]) }}" target="_blank">Перейти по ссылке</a>
                    </div>
                    {{--<div>
                        <iframe src="{{ route('cloudpayments.thank_you', [$product->id]) }}" frameborder="0" style="width: 100%; height: 100vh"></iframe>
                    </div>--}}
                </div>
            </div>

            <div class="form-group row">
                <label for="prices" class="col-sm-2 col-form-label">Связанные продукты для рекламы в thank you page</label>
                <div class="col-sm-10">
                    <product-additionals-component :product-additionals-prop="{{ json_encode($productAdditionals) }}" :additionals-prop="{{ json_encode($additionals) }}"></product-additionals-component>
                </div>
            </div>

            <div class="form-group">
                <input type="submit" value="Сохранить" class="btn btn-success" />
            </div>
        </form>
        <a href="{{ route('products.index') }}">К списку</a>
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
