<?php

namespace App\Http\Requests\Card;

use App\Services\Constant\GlobalConstant as GCon;
use App\Services\Constant\CardConstant as Con;
use Illuminate\Foundation\Http\FormRequest;

class CardSearchRequest extends FormRequest
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
            GCon::NAME => 'nullable|string|max:255',
            Con::SET => 'nullable|string|max:255',
            Con::COLOR => 'nullable|string|in:W,U,B,R,G,L,Land,Art,T,M',
            Con::IS_FOIL => 'boolean',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            Con::IS_FOIL => filter_var($this->isFoil, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
        ]);
    }
}
