<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentRequest extends FormRequest
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
            'amount' => 'required',
            // 'from' => 'required',
            // 'to' => 'required',
            'quantity' => 'required',
            'file' => 'required_if:type,==,transfer',
            // 'type' => 'required',
            // 'start_date' => 'date|nullable|required_if:recurrent,on',
            // 'period' => 'required_if:recurrent,on',
            // 'interval' => 'required_if:recurrent,on',
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
            'code' => 'Код',
            'title' => 'Заголовок',
            'description' => 'Описание',
            'price' => 'Цена',
        ];
    }
}
