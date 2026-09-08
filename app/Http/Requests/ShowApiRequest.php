<?php

namespace App\Http\Requests;

use App\Services\Constant\GlobalConstant;
use Illuminate\Foundation\Http\FormRequest;

/**
 * 各APIのShowメソッドに関するRequestクラス
 */
class ShowApiRequest extends FormRequest
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
            GlobalConstant::ID => 'required|integer|min:1',
        ];
    }

        /**
     * バリデーション前にルートパラメータをマージする
     */
    protected function prepareForValidation(): void
    {
          $routeParameter = collect($this->route()->parameters())->first();
        $this->merge([
            GlobalConstant::ID => $routeParameter,
        ]);
    }

    public function attributes()
    {
        return [
            GlobalConstant::ID => 'ID',
        ];
    }
}
