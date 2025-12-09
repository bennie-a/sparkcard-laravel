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
    const FILE = 'file';

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
            self::FILE => ['required', 'file', 'mimes:csv,txt',
            function ($attribute, $value, $fail) {
                if (!$this->hasFile(self::FILE)) {
                    $fail('validation.file.required');
                }
                if ($this->file(self::FILE)->getSize() === 0) {
                    $fail(__('validation.file.empty-content'));
                }
            }],
        ];
    }

    /**
     * アップロードされたファイルを読み込む。
     *
     * @return void
     */
    public function prepareForValidation()
    {
        $path = $this->file(self::FILE)->path();
        logger()->info('ファイル読み込み開始：', [$path]);
        $reader = new ShiptLogCsvReader();
        $records = $reader->read($path);
        return $this->merge([
            GC::DATA => $records,
        ]);
    }
}
