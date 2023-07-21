<?php

namespace App\Http\Requests;

use App\Rules\Halfsize;
use Illuminate\Foundation\Http\FormRequest;

class PostCardDBRequest extends FormRequest
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
            'setCode'=>'required',
            'name'=>'required',
            'en_name'=>'required',
            'color'=>'required',
            'multiverseId' => 'required_without_all:scryfallId,imageurl|integer',
            'scryfallId' => ['required_without_all:multiverseId,imageurl', new Halfsize],
            'number' => 'required',
            'imageurl' => 'required_without_all:multiverseId,scryfallId',
            'isSkip' => 'nullable|boolean'
        ];
    }

    public function attributes()
    {
        return [
            'setCode'=>'セット略称',
            'name'=>'カード名',
            'en_name'=>'カード名(英語)',
            'color'=>'色',
            'multiverseId' => 'Multiverse ID',
            'scryfallId' => 'Scryfall ID',
            'number' => 'カード番号',
            'imageurl' => '画像URL',
        ];
    }


    /**
     * エラーメッセージを変更
     * 
     * @return array
     */
    public function messages()
    {
        return [
            'setCode.required' => 'エキスパンション略称は必ず入力してください。',
            'name.required' => 'カード名は必ず入力してください。',
            'en_name.required' => 'カード名(英名)は必ず入力してください。',
            'color.required' => '色は必ず入力してください。'
        ];
    }

}
