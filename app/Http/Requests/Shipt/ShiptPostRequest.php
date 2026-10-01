<?php

namespace App\Http\Requests\Shipt;

use App\Rules\DateFormatRule;
use App\Rules\Halfsize;
use App\Rules\PlatformRule;
use App\Rules\PostalCodeRule;
use App\Services\Constant\GlobalConstant;
use App\Services\Constant\ShiptConstant as ShiptCon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * 出荷情報登録に関するRequestクラス
 */
class ShiptPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ShiptCon::PLATFORM => ['required', PlatformRule::rule()],
            ShiptCon::ORDER_ID => ['required', new Halfsize()],
            ShiptCon::BUYER => 'required',
            ShiptCon::SHIPT_DATE => ['required', DateFormatRule::slashRules()],
            ShiptCon::ZIPCODE => ['required', PostalCodeRule::rules()],
            ShiptCon::ADDRESS => 'required|string',
            ShiptCon::ITEM_SUBTOTAL => ['required', 'integer', 'min:1'],
            ShiptCon::DISCOUNT_AMOUNT => ['required', 'integer', 'min:0'],
            ShiptCon::GRAND_TOTAL => ['required', 'integer', 'min:1'],
            ShiptCon::ITEMS => ['required','array', 'min:1'],
            ShiptCon::FEE => ['required', 'array', 'size:1'],
            ShiptCon::FEE.'.'.GlobalConstant::ID => ['required', 'integer','min:1', 'exists:shipping,id'],
            ShiptCon::ITEMS.'.*.'.GlobalConstant::ID => ['required', 'integer','min:1', 'exists:stockpile,id'],
            ShiptCon::ITEMS.'.*.'.ShiptCon::SHIPMENT => ['required','integer','min:1'],
            ShiptCon::ITEMS.'.*.'.ShiptCon::TOTAL_PRICE => ['required', 'integer', 'min:1'],
            ShiptCon::ITEMS.'.*.'.ShiptCon::SINGLE_PRICE => ['required', 'integer','min:1'],
            ShiptCon::ITEMS.'.*.'.ShiptCon::IS_REGISTERED => 'required|boolean',
        ];
    }

    public function passedValidation()
    {
        $info = $this->only([ShiptCon::ORDER_ID, ShiptCon::BUYER, ShiptCon::ZIPCODE,
                                         ShiptCon::ADDRESS, 'shipping_date', ShiptCon::ITEMS]);
        $this->merge([
            GlobalConstant::DATA => $info,
        ]);
    }

    public function attributes()
    {
        return [
            ShiptCon::ADDRESS => '住所',
            ShiptCon::ZIPCODE => '郵便番号',
            ShiptCon::ITEM_SUBTOTAL => '合計金額',
            ShiptCon::DISCOUNT_AMOUNT => 'クーポン割引額',
            ShiptCon::GRAND_TOTAL => '最終請求金額',
            ShiptCon::ITEMS => '商品情報',
            ShiptCon::FEE => '送料',
            ShiptCon::FEE.'.'.GlobalConstant::ID => '送料ID',
            ShiptCon::ITEMS.'.*.'.GlobalConstant::ID => '在庫ID',
            ShiptCon::ITEMS.'.*.'.ShiptCon::SHIPMENT => '出荷枚数',
            ShiptCon::ITEMS.'.*.'.ShiptCon::SINGLE_PRICE => '1枚あたりの単価',
            ShiptCon::ITEMS.'.*.'.ShiptCon::TOTAL_PRICE => '支払い金額',
            ShiptCon::ITEMS.'.*.'.ShiptCon::IS_REGISTERED => '登録済みフラグ',
        ];
    }

    public function messages()
    {
        return [
            "platform.in" =>"プラットフォームは'mercari'もしくは'base'を入力してください。"
        ];
    }
}
