<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostNotionCardRequest extends FormRequest
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
            'name' => 'required',
            'enname' => 'required',
            'set' => 'required',
            'stock' => 'required | integer | min:1', 
            'imageUrl' => 'required',
            'index'  => 'required | integer | min:1',
            'price' => 'required | integer | min:1',
            'color' => 'required',
            'isFoil' => 'required | regex:/^[true|false]',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'カード名',
            'set' => 'エキスパンション名(略称)',
            'stock' => '枚数',
            'imageUrl' => '画像URL',
            'index' => 'カード番号',
            'price' => '価格',
            'color' => '色',
            'isFoil' => '通常版orFoil',
        ];
    }

    public function messages()
    {
        return  ['stock.required' => ':attributeは必ず入力してください。',
        'stock.min' => ':attributeは1枚以上を入力してください。',
        'imageUrl.required' => ':attributeは必ず入力してください。できればscryfall.comの画像を使ってほしいです。'];
    }
}
