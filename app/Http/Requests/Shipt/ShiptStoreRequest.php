<?php

namespace App\Http\Requests\Shipt;

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
            //
        ];
    }
}
