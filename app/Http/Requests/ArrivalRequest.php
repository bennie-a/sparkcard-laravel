<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 入荷手続きのリクエストクラス
 */
class ArrivalRequest extends FormRequest
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
            'id' =>'required|numeric',
            'name' =>'required',
            'enname' =>'required',
            'index' =>'required',
            'price' =>'required|numeric',
            'attr' =>'required',
            'color' =>'required',
            'imageUrl' =>'nullable',
            'quantity' =>'required|numeric',
            'isFoil' =>'required|boolean',
            'language' =>'required|in:JP,EN,IT,CS,CT',
            'condition' =>'required|in:NM,NM-,EX+,EX,PLD',
        ];
    }
}
