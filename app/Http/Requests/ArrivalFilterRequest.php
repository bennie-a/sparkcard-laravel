<?php

namespace App\Http\Requests;

use App\Services\Constant\SearchConstant;
use Illuminate\Foundation\Http\FormRequest;
use App\Services\Constant\StockpileHeader as HEAD;
use App\Traits\VendorTypeIdRules;

/**
 * エンドポイントが'api/arrival/filtering'のRequestクラス
 */
class ArrivalFilterRequest extends FormRequest
{
    use VendorTypeIdRules;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            HEAD::ARRIVAL_DATE => ['required', 'date'],
            HEAD::VENDOR_TYPE_ID => self::vendorTypeIdRules(),
            SearchConstant::CARD_NAME => ['nullable', 'string', 'max:255']
        ];
    }
}
