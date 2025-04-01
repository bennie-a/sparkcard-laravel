<?php

namespace App\Http\Requests;

use App\Traits\VendorTypeIdRules;
use Illuminate\Foundation\Http\FormRequest;

/**
 * 入荷手続きのリクエストクラス
 */
class ArrivalRequest extends FormRequest
{
    use VendorTypeIdRules;
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
            'card_id' =>'required|numeric',
            'language' =>'required|in:JP,EN,IT,CS,CT',
            'cost' =>'required|numeric|min:1',
            'vendor_type_id' => self::vendorTypeIdRules(),
            'vendor' =>['nullable', 'string', 'required_if:vendor_type_id,3'],
            'market_price' =>'required|numeric|min:1',
            'quantity' =>'required|numeric|min:1',
            'condition' =>'required|in:NM,NM-,EX+,EX,PLD',
            'arrival_date' => 'required|date'
        ];
    }

    public function attributes()
    {
        return [
            'card_id' => 'カード情報ID',
            'language' => '言語',
            'cost' =>'原価',
            'market_price' =>'相場価格',
            'quantity' => '枚数',
            'condition' => '状態',
            'vendor' =>'入荷先名',
            'isFoil' =>'通常/Foil',
        ];
    }

}
