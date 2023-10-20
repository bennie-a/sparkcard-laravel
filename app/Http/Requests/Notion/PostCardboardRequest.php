<?php

namespace App\Http\Requests\Notion;

use App\Http\Requests\ValidationRules;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostCardboardRequest extends FormRequest
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
    public function rules(ValidationRules $rules)
    {
        $commonRules = $rules->getRules(['cardboard.status']);
        $commonRules['name'] = 'required';
        return $commonRules;
    }

    protected function failedValidation(Validator $validator) {
            $res = response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ], 400);
        throw new HttpResponseException($res);
    }

    public function messages() {
        return [
            'cardboard.status.in' => 'Statusは商品管理ボードのStatusのどれかを入力してください。'
        ];
    }

    public function attributes()
    {
        return [
            'cardboard.status' => 'Status'
        ];
    }
}
