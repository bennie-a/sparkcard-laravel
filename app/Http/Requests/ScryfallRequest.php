<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScryfallRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'setcode' => 'required|alpha_num',
            'number' => 'required|alpha_num',
        ];
    }

    public function attributes()
    {
        return [
            'setcode' => 'セット略称',
            'number' => 'カード番号',
        ];
    }
}
