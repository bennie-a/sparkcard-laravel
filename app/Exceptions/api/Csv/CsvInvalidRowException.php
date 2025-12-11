<?php

namespace App\Exceptions\api\Csv;
use App\Exceptions\ApiException;
use App\Http\Response\CustomResponse;

/**
 * CSVファイルのデータ行に関する例外クラス
 */
class CsvInvalidRowException extends ApiException {

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    /**
     * @override ApiException
     * @return string
     */
    public function getTitle(): string
    {
        return '不正な記載';
    }

    public function getDetail(): string
    {
        return 'CSVファイルに不正な記載があります。';
    }

    /**
     * @override ApiException
     * @return 442
     */
    public function getStatusCode(): int
    {
        return CustomResponse::HTTP_CSV_VALIDATION;
    }

    public function getRows(): array
    {
        return $this->rows;
    }
}
