<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CardFileRequest extends FormRequest
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
        $rules = ['data' => 'required', 
                            'data.cards' => 'required',
                             'data.code' => 'required',
                             'isDraft' => 'required',
                            'color' => 'nullable:string'];

        return $rules;
    }

    /**
     * エラーメッセージを変更
     * 
     * @return array
     */
    public function messages()
    {
        return [
            'data.cards.required' => 'cardsオブジェクトはdataオブジェクト内に指定してください。',
            'data.code.required' => 'codeはdataオブジェクト内に指定してください',
        ];
    }

    public function attributes()
    {
        return [
            'isDraft' => '通常版フィルター',
            'color' => '色'
        ];
    }

    protected function prepareForValidation() {
        $value = $this->isDraft;
        # 文字列表現のboolを実際のboolに変換
        if ($value === 'false') {
            $value = false;
        } elseif ($value === 'true') {
            $value = true;
        }
        $this->merge(['isDraft' => $value]);
    }
}
