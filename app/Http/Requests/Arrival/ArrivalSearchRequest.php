<?php

namespace App\Http\Requests\Arrival;

use App\Services\Constant\SearchConstant;
use Illuminate\Foundation\Http\FormRequest;
use App\Services\Constant\ArrivalConstant as Con;
use App\Services\Constant\SearchConstant as SCon;
use App\Services\Constant\StockpileHeader as HEAD;
use App\Traits\VendorTypeIdRules;

/**
 * エンドポイントが'api/arrival', メソッドが'GET'のRequestクラス
 */
class ArrivalSearchRequest extends FormRequest
{
    use VendorTypeIdRules;
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
            Con::ARRIVAL_DATE => ['date'],
            SCon::VENDOR_TYPE_ID => self::vendorTypeIdRules(),
            SearchConstant::CARD_NAME => ['nullable', 'string', 'max:255']
        ];
    }
}
