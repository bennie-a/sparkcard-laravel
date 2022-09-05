<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class CardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'enname' => 'required',
            'price' => 'required',
            'index' => 'required',
            'attr' => 'required',
            'color'=>'required',
            'imageurl'=>'required'
        ];
    }
    /*
    * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $data = [
            'message' => __('The given data was invalid.'),
            'errors' => $validator->errors()->toArray(),
        ];

        throw new HttpResponseException(response()->json($data, Response::HTTP_BAD_REQUEST));
    }
}
