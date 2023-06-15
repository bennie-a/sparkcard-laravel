<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class CsvImportRequest extends FormRequest
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
            'path' => 'required|regex:/\.csv$/',
        ];
    }

    public function attributes()
    {
        return [
            'path' => 'ファイルパス'
        ];
    }

    public function messages()
    {
        return [
            'path.required' => ':attributeは必ず入力してください。',
            'path.regex' => '入力した:attributeはCSVファイルではありません。',
        ];
    }

        /*
    * Handle a failed validation attempt.
     *
     * @param Validator  $validator
     * @return HttpResponseException
    */
    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'status' => 'validation error',
            'errors' => $validator->errors()
        ], Response::HTTP_BAD_REQUEST);
        throw new HttpResponseException($response);
    }
}
