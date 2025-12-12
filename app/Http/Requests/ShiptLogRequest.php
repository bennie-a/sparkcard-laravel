<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\Constant\ShiptConstant as SC;
use App\Services\Constant\StockpileHeader as Header;

/**
 * 出荷情報の検索リクエストクラス
 */
class ShiptLogRequest extends FormRequest
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
            SC::BUYER => 'nullable',
            SC::SHIPPING_DATE => 'date',
        ];
    }
}
