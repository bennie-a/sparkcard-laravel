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
            Acon::ARRIVAL_DATE => 'date|before_or_equal:today',
            SearchConstant::VENDOR_TYPE_ID => ['integer', 'between:1,5'],
            Acon::VENDOR => 'nullable|string',
            Header::COST => 'numeric|min:1',
        ];
    }

    public function attributes()
    {
        return [
            Header::QUANTITY => '入荷枚数'
        ];
    }

    public function withValidator($validator)
    {
    $validator->after(function ($validator) {
            $vendorTypeId = $this->input(SearchConstant::VENDOR_TYPE_ID);
            $vendor = $this->input(Acon::VENDOR);

            if ($vendorTypeId == 3 && (is_null($vendor) || $vendor === '')) {
                $validator->errors()->add(Acon::VENDOR, '取引先カテゴリIDが「買取」の時は取引先は必ず入力してください。');
            }

            if ($vendorTypeId != 3 && !is_null($vendor) && $vendor !== '') {
                $validator->errors()->add(Acon::VENDOR, '取引先カテゴリIDが「買取」以外の時は取引先は入力しないでください。');
            }
        });
    }
}
