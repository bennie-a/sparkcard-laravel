<?php

namespace App\Http\Requests;

use App\Services\Constant\StockpileHeader;
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
        $rules = [
                            'data' => 'required',
                            'data.cards' => 'required_without:data.tokens',
                            'data.tokens' => 'required_without:data.cards',
                            'data.code' => 'required',
                            'isDraft' => 'nullable|boolean',
                            'color' => 'nullable|string',
                            StockpileHeader::SETCODE => 'required'
                        ];

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->input('data', []);

            if (!empty($data['cards']) && !empty($data['tokens'])) {
                $validator->errors()->add(
                    'data.cards',
                    'cardsとtokensは同時に指定できません。'
                );
            }
        });
    }

    public function passedValidation()
    {
        $info = $this->only(['data.cards', 'data.tokens']);
        if (!empty($info['data']['tokens'])) {
            $this->merge([
                'data.cards' => $info['data']['tokens'],
            ]);
        }
    }

    /**
     * エラーメッセージを変更
     *
     * @return array
     */
    public function messages()
    {
        return [
            'data.cards.required_without' => 'cardsまたはtokensのどちらかは必須です。',
            'data.tokens.required_without' => 'cardsまたはtokensのどちらかは必須です。',

            'data.code.required' => 'codeはdataオブジェクト内に指定してください',
        ];
    }

    public function attributes()
    {
        return [
            'isDraft' => '通常版フィルタースイッチ',
            'color' => '色フィルター'
        ];
    }

    protected function prepareForValidation() {
        $value = $this->isDraft;
        # 文字列表現のboolを実際のboolに変換
        if (empty($value) || $value === 'false' || $value == '0') {
            $value = false;
        } elseif ($value == 1 || $value === 'true') {
            $value = true;
        }
        $this->merge(['isDraft' => $value]);

        $color = $this->color;
        if (empty($color)) {
            $color = '';
        }
        $this->merge(['color' => $color]);
    }
}
