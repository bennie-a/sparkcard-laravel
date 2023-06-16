<?php
namespace App\Traits;

use App\Exceptions\CsvValidationException;
use App\Http\Requests\CsvImportRequest;
use Illuminate\Http\Response;

/**
 * CSVファイルからDBに登録するTrait
 */
trait ImportCsv {

    public $service;

    public function import(CsvImportRequest $request) {
        $path = $request->input('path');
        $result = $this->service->import($path);
        return response()->json($result, Response::HTTP_CREATED);
    }
}