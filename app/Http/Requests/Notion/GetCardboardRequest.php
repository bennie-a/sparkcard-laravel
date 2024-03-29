<?php

namespace App\Http\Requests\Notion;

use App\Http\Requests\ValidationRules;
use App\Services\Constant\SearchConstant as Con;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * 商品管理ボードの検索条件に関するリクエストクラス
 */
class GetCardboardRequest extends PostCardboardRequest
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
    public function rules(ValidationRules $rules)
    {
        $commonRules =$rules->getRules([
            'cardboard.status' => ['required'],
        ]);
        $commonRules['price'] = 'required|integer';
        return $commonRules;
    }

    public function attributes()
    {
        $attrs = parent::attributes();
        $attrs[ 'price'] = '最低価格';
        return $attrs;
    }

    /**
     * 入力パラメータを取得する。
     *
     * @return array
     */
    public function getParams():array {
        $details = $this->only([Con::STATUS,  Con::SET_NAME]);
        $details[Con::PRICE] = (int)$this->input(Con::PRICE);
        return $details;
    }
}
