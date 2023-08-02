<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockpileIndexRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'card_name' => 'nullable|string',
            'set_name' => 'nullable|string',
            'limit' => 'numeric|min:0',
        ];
    }

    public function attributes()
    {
        return [
            'card_name' => 'カード名',
            'set_name' => 'セット名',
            'limit' => '取得件数'
        ];
    }
}
