<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'account' => 'required',
            'email' => 'required|email',
            'phone' => 'required|numeric|regex:/^\+?(\d{11})$/',
            'role_id' => 'required|numeric|exists:roles,id',
            'pass' => 'required',
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
            'max' => ':attribute не может превышать :max символов.'
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
            'name' => 'Имя',
            'phone' => 'Телефон',
            'email' => 'Email',
            'comments' => 'Комментарий',
            'remark_id' => 'Ремарка',
        ];
    }
}
