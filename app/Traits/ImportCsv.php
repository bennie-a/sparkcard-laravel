<?php
namespace App\Traits;

use App\Http\Requests\CsvFilePathRequest;
use App\Http\Requests\CsvImportRequest;
use Illuminate\Http\Response;

/**
 * CSVファイルからDBに登録するTrait
 */
trait ImportCsv {

    public $service;

    public function import(CsvImportRequest $request) {
        $path = $request->input('path');
        logger('ファイルパス', [$path]);
        return response('import ok', Response::HTTP_CREATED);
    }
}