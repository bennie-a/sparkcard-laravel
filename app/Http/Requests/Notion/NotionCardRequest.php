<?php

namespace App\Http\Requests\Notion;

use Illuminate\Foundation\Http\FormRequest;

class NotionCardRequest extends FormRequest
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
            'status'=>'required | in:ロジクラ要登録,販売保留'
        ];
    }
}
