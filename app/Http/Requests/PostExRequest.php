<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * エキスパンション登録APIのリクエストクラス
 */
class PostExRequest extends FormRequest
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
            'attr' => 'required',
            'block' => 'required',
            'format' => ['required', 'regex:/^[スタンダード|パイオニア|モダン|レガシー|統率者|マスターピース|その他]+$/u'],
            'release_date' => 'date'
        ];
    }

    public function attributes()
    {
        return [
         'name' => '名称',
        'attr' => '略称',
        'block' => 'ブロック',
        'format' => 'フォーマット',
        'release_date' => 'リリース日'
    ];
    }
}
