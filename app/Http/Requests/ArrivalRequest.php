<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 入荷手続きのリクエストクラス
 */
class ArrivalRequest extends FormRequest
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
            'id' =>'required|numeric',
            'name' =>'required',
            'enname' =>'required',
            'index' =>'required',
            'price' =>'required|numeric',
            'attr' =>'required',
            'color' =>'required',
            'imageUrl' =>'nullable',
            'stock' =>'required|numeric|min:1',
            'isFoil' =>'required|boolean',
            'language' =>'required|in:日本語,英語,イタリア語,簡体中国語,繁体中国語',
            'condition' =>'required|in:NM,NM-,EX+,EX,PLD',
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'ID',
            'name' => 'カード名',
            'enname' => 'カード名(英語)',
            'color' => '色',
            'attr' => 'セット名(略称)',
            'imageUrl' => '画像URL',
            'language' => '言語',
            'stock' => '枚数',
            'condition' => '状態'
        ];
    }

}
