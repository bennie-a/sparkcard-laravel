<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\Constant\SearchConstant as Con;

class ArrivalSearchRequest extends FormRequest
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
            Con::START_DATE =>  ['nullable', 'date', function($attribute, $value, $fail){
                $endDate = $this->input(Con::END_DATE);
                if (!empty($endDate) && !empty($value) && $endDate < $value) {
                    $fail(__('validation.before_or_equal', ['attribute' => __('validation.attributes.end_date'), 'date' => __('validation.attributes.start_date')]));
                }
            }],
            Con::END_DATE => ['nullable', 'date'],
        ];
    }

    public function attributes()
    {
        return [
            Con::CARD_NAME => 'カード名',
            Con::START_DATE => '入荷日(開始日)',
            Con::END_DATE => '入荷日(終了日)',
        ];
    }
}
