<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\Constant\BaseApiConstant as BCon;
class BaseCallbackRequest extends FormRequest
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
            BCon::AUTH_CODE => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            BCon::AUTH_CODE => '認可コード',
        ];
    }
}
