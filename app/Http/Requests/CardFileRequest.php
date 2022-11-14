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
        $rules = ['data' => 'required', 'data.cards' => 'required', 'data.code' => 'required'];

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
            'data.required' => 'dataオブジェクトは必ず入力してください。',
            'data.cards.required' => ':cardsオブジェクトはdataオブジェクト内に指定してください。',
            'data.code.required' => 'codeはdataオブジェクト内に指定してください',
        ];
    }

    /**
     * エラーメッセージを出力
     * 
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        $response['errors'] = $validator->errors()->toArray();

        throw new HttpResponseException(
            response()->json($response, 422, [], JSON_UNESCAPED_UNICODE)
        );
    }

}
