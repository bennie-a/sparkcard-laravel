<?php

namespace App\Http\Requests;

use App\Rules\Halfsize;
use App\Services\Constant\CardConstant;
use App\Traits\UuidRules;
use Illuminate\Foundation\Http\FormRequest;

class PostCardDBRequest extends FormRequest
{
    use UuidRules;
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
            'multiverseId' => 'integer',
            'scryfallId' => [self::uuidrules()],
            'number' => 'required',
            'is skip' => 'nullable|boolean',
            'foiltype' => 'required',
            CardConstant::PROMO_ID => 'required|integer',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $multiId = $this->input(CardConstant::MULTIVERSEID);
            $scryId = $this->input(CardConstant::SCRYFALLID);
            $imageurl = $this->input(CardConstant::IMAGE_URL);
            if (empty($multiId) && empty($scryId) && empty($imageurl)) {
                $validator->errors()->add('multiverseId/scryfallId/imageurl', 'multiverseIdかscryfallIdかimage_urlのいずれかを指定してください。');
            }
        });
    }

    public function attributes()
    {
        return [
            'setCode'=>'セット略称',
            'en_name'=>'カード名(英語)',
            'color'=>'色',
            'multiverseId' => 'Multiverse ID',
            'scryfallId' => 'Scryfall ID',
            'imageurl' => '画像URL',
            'foiltype' => 'カード仕様'
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
