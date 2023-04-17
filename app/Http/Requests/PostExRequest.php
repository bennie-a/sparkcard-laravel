<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

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
            'release_date' => 'nullable | date'
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

    public function messages()
    {
        return [
            'name.required' => ':attributeを入力してください。',
            'attr.required' => ':attributeを入力してください。',
            'block.required' => ':attributeを入力してください。',
            'format.required' => ':attributeはスタンダード、パイオニア、モダン、レガシー、統率者、マスターピース、その他のどれかを入力してください。',
            'format.regex' => ':attributeはスタンダード、パイオニア、モダン、レガシー、統率者、マスターピース、その他のどれかを入力してください。',
        ];
    }
    /*
    * Handle a failed validation attempt.
     *
     * @param Validator  $validator
     * @return HttpResponseException
    */
    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'status' => 'validation error',
            'errors' => $validator->errors()
        ], Response::HTTP_BAD_REQUEST);
        throw new HttpResponseException($response);
    }
}
