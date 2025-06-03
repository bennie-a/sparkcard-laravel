<?php

namespace App\Http\Requests\Arrival;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\ArrivalConstant as ACon;
use App\Services\Constant\SearchConstant;
use App\Traits\Rules\QtyRules;
use App\Traits\Rules\SupplierRules;
use App\Traits\VendorTypeIdRules;

class ArrivalUpdateRequest extends FormRequest
{
    use VendorTypeIdRules;
    use QtyRules;
    use SupplierRules;
    
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
            Header::QUANTITY => self::qtyRules(),
            Acon::ARRIVAL_DATE => 'date',
            SearchConstant::VENDOR_TYPE_ID => ['integer', 'between:1,5'],
            Acon::VENDOR => self::supplierRules(),
            Header::COST => 'numeric|min:1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (empty($this->all())) {
                $validator->errors()->add('request', '更新項目が必要です。');
            }
        });
    }
}
