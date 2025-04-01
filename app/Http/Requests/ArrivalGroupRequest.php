<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\Constant\SearchConstant as Con;
/**
 * エンドポイントがapi/arrival/grouping/のリクエストクラス
 */
class ArrivalGroupRequest extends FormRequest
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
            Con::CARD_NAME => 'required_without_all:start_date,end_date',
            Con::START_DATE =>  ['nullable', 'date', function($attribute, $value, $fail){
                $endDate = $this->input(Con::END_DATE);
                if (request()->has(Con::END_DATE) && !empty($value) && $endDate < $value) {
                    $fail(__('validation.before_or_equal', 
                    ['attribute' => $this->attributes()[Con::START_DATE], 'date' => $this->attributes()[Con::END_DATE]]));
                }
            }],
            Con::END_DATE => ['nullable', 'date'],
        ];
    }
    
    public function attributes()
    {
        return [
            Con::START_DATE => '入荷日(開始日)',
            Con::END_DATE => '入荷日(終了日)',
        ];
    }

    public function messages()
    {
        return [
            "card_name.required_without_all" => 'カード名 / 入荷日(開始日) / 入荷日(終了日)の中で1個以上の項目を必ず入力してください。',
        ];
    }
}
