<?php
namespace App\Traits;

use App\Exceptions\CsvValidationException;
use App\Http\Requests\CsvFileRequest;
use App\Http\Response\CustomResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * CSVファイルからDBに登録するTrait
 */
trait ImportCsv {

    public $service;

    public function import(CsvFileRequest $request) {
            $path = $request->input('path');
            $result = $this->service->import($path);
            $code = Response::HTTP_CREATED;
            
            $successCount = $result['successful_rows'];
            $errorCount = $result['failed_rows'];
            // 一部成功
            if ($errorCount > 0 && $successCount  > 0) {
                $code = Response::HTTP_MULTI_STATUS;
                // 全件失敗
            } else if ($errorCount > 0 && $successCount == 0) {
                $code = CustomResponse::HTTP_IMPORT_ERROR;
            }
            return response()->json($result, $code);
    }
}