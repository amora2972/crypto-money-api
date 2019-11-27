<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
    public static function rules()
    {
        return [
            'currency_id' => 'required|integer',
            'amount' => 'required|numeric|min:1',
            'total' => 'required|numeric',
            'buying_rate' => 'required|numeric|min:0.1'
        ];
    }
}
