<?php
namespace App\Traits;

use App\Exceptions\CsvValidationException;
use App\Http\Requests\CsvFileRequest;
use Illuminate\Http\Response;

/**
 * CSVファイルからDBに登録するTrait
 */
trait ImportCsv {

    public $service;

    public function import(CsvFileRequest $request) {
        $path = $request->input('path');
        $result = $this->service->import($path);
        return response()->json($result, Response::HTTP_CREATED);
    }
}