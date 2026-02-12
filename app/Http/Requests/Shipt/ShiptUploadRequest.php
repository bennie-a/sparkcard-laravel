<?php

namespace App\Http\Requests\Shipt;

use App\Files\Reader\ShiptLogCsvReader;
use App\Services\Constant\GlobalConstant as GC;
use Illuminate\Foundation\Http\FormRequest;

/**
 * 注文CSVファイルのアップロードに関するRequestクラス
 */
class ShiptUploadRequest extends FormRequest
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
            GC::FILE => ['required', 'file',
                                        function ($attribute, $value, $fail) {
                                            $ja = $this->attributes()[GC::FILE];
                                            if ($value->getClientOriginalExtension() !== 'csv') {
                                                $fail('ファイルはCSV形式でアップロードしてください');
                                            }
                                            if ($this->file(GC::FILE)->getSize() === 0) {
                                                $fail('ファイルが空です');
                                            }
                                        }],
        ];
    }

    /**
     * アップロードされたファイルを読み込む。
     *
     * @return void
     */
    public function passedValidation()
    {
        $path = $this->file(GC::FILE)->path();
        logger()->info('ファイル読み込み開始：', [$path]);
        $reader = new ShiptLogCsvReader();
        $records = $reader->read($path);
        return $this->merge([
            GC::DATA => $records,
        ]);
    }

    public function attributes()
    {
        return [
            GC::FILE => 'ファイル'
        ];
    }
}
