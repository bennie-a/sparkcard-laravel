<?php

namespace App\Http\Requests\Notion;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class NotionCardRequest extends FormRequest
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
            'status'=>'in:ロジクラ要登録,販売保留,要写真撮影,撮影済み,BASE登録予定'
        ];
    }

    protected function failedValidation(Validator $validator) {
            $res = response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ], 400);
        throw new HttpResponseException($res);
    }

    public function messages() {
        return [
            'status.in' => 'Statusは商品管理ボードのStatusのどれかを入力してください。'
        ];
    }
}
