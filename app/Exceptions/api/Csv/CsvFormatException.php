<?php
namespace App\Exceptions\api\Csv;

use App\Exceptions\ApiException;
use App\Http\Response\CustomResponse;

/**
 * CSVファイルにフォーマットエラーが
 * 発生した時の例外クラス
 */
class CsvFormatException extends ApiException {

    private string $title;
    private string $detail;
    private array $rows = [];

    public function __construct(string $keyword, string $value = '', array $rows = [])
    {
        $validation = 'validation.file';
        $this->title = __("$validation.title.$keyword");
        $this->detail = __("$validation.detail.$keyword", ['values' => $value]);
        $this->rows = $rows;
    }

    /**
     * @override ApiException
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @override ApiException
     * @return 442
     */
    public function getStatusCode(): int
    {
        return CustomResponse::HTTP_CSV_VALIDATION;
    }

    public function getDetail(): string
    {
        return $this->detail;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

}
