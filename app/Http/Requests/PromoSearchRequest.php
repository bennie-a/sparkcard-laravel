<?php

namespace App\Http\Requests;

use App\Rules\Halfsize;
use Illuminate\Foundation\Http\FormRequest;
use App\Services\Constant\StockpileHeader as Header;


class PromoSearchRequest extends FormRequest
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
            Header::SETCODE => [['required', 'regex:/^[a-zA-Z0-9]+$/']]
        ];
    }
}
