<?php
namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * CSVファイルからDBに登録するTrait
 */
trait ImportCsv {

    public $service;

    public function import(Request $request) {
        return response('import ok', Response::HTTP_CREATED);
    }
}