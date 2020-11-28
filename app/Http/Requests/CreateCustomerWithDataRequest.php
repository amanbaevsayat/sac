<?php

namespace App\Http\Requests;

use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Foundation\Http\FormRequest;

class CreateCustomerWithDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'customer' => 'required|array',
            'customer.name' => 'required',
            'customer.phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|starts_with:+',
            // 'customer.phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|starts_with:+|unique:customers,phone',
            'customer.email' => 'email|nullable',
            'subscriptions' => 'required|array',
            'subscriptions.*.product_id' => 'required|exists:products,id',
            'subscriptions.*.price' => 'required',
            'subscriptions.*.payment_type' => 'required|in:' . implode(',', array_keys(Subscription::PAYMENT_TYPE)),
            'subscriptions.*.status' => 'required|in:' . implode(',', array_keys(Subscription::STATUSES)),
            'subscriptions.*.period' => 'nullable|in:' . implode(',', array_keys(Payment::QUANTITIES)),
            'subscriptions.*.started_at' => 'required|date',
            'subscriptions.*.ended_at' => 'date|nullable',
            'subscriptions.*.payments.*.check' => 'url|nullable',
            // date|date_format:Y-m-d\TH:i:sP
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required' => ':attribute не может быть пустым.',
            'required_without' => ':attribute не может быть пустым если :values не передан.',
            'email' => 'Наберите правильный формат email.',
            'size' => 'Длина :attribute должен быть ровно :size.',
            'integer' => ':attribute должен быть числовым значением.',
            'min' => 'Выберите правилный :attribute.',
            'max' => ':attribute не может превышать :max символов.',
            'regex' => ':attribute заполнен не корректно.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'customer.name' => 'Имя',
            'customer.phone' => 'Телефон',
            'customer.email' => 'Email',
            'customer.comments' => 'Комментарий',
            'subscriptions.*.product_id' => 'Услуга',
            'subscriptions.*.payment_type' => 'Тип оплаты',
            'subscriptions.*.status' => 'Статус',
            'subscriptions.*.started_at' => 'Дата старта',
            'subscriptions.*.price' => 'Цена',
        ];
    }
}
