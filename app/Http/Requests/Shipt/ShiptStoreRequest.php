<?php

namespace App\Http\Requests\Shipt;

use App\Libs\CarbonFormatUtil;
use App\Rules\DateFormatRule;
use App\Rules\Halfsize;
use App\Rules\PostalCodeRule;
use App\Services\Constant\GlobalConstant;
use App\Services\Constant\ShiptConstant as ShiptCon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * 出荷情報登録に関するRequestクラス
 */
class ShiptStoreRequest extends FormRequest
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
            ShiptCon::ORDER_ID => ['required', new Halfsize()],
            ShiptCon::BUYER => 'required',
            ShiptCon::SHIPPING_DATE => DateFormatRule::slashRules(),
            ShiptCon::POSTAL_CODE => ['required', PostalCodeRule::rules()],
            ShiptCon::ADDRESS => 'required|string',
            ShiptCon::ITEMS.'.*.'.GlobalConstant::ID => 'required|numeric|min:1',
            // ShiptCon::ITEMS.'.*.'.ShiptCon::SHIPMENT => 'required|numeric|min:1',
            ShiptCon::ITEMS.'.*.'.ShiptCon::TOTAL_PRICE => 'required|numeric|min:50',
            ShiptCon::ITEMS.'.*.'.ShiptCon::SINGLE_PRICE => 'required|numeric|min:1',
        ];
    }

    public function passedValidation()
    {
        $shiptdate = $this->input(ShiptCon::SHIPPING_DATE, CarbonFormatUtil::today());
        $buyer = $this->only([ShiptCon::ORDER_ID, ShiptCon::BUYER,
                                        ShiptCon::POSTAL_CODE, ShiptCon::ADDRESS]);
        $buyer[ShiptCon::SHIPPING_DATE] = $shiptdate;
        $this->merge([
            ShiptCon::BUYER_INFO => $buyer,
            ShiptCon::ITEMS => $this->input(ShiptCon::ITEMS),
        ]);
    }
}
