<?php
namespace App\Http\Controllers\Shipt;

use App\Exceptions\api\Csv\CsvInvalidRowException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shipt\ShiptUploadRequest;
use App\Http\Resources\Shipt\CsvOrderResource;
use App\Services\Shipt\ShiptLogCsvService;
use Illuminate\Http\Response;
use App\Services\Constant\GlobalConstant as GC;

/**
 * 注文情報に関するCSVデータのクラス
 */
class ShiptLogCsvController extends Controller
{
    public function __construct(private ShiptLogCsvService $service) {
        ini_set("max_execution_time",300); // タイムアウトを240秒にセット
        ini_set("max_input_time",300); // パース時間を240秒にセット
    }


    /**
     * 注文CSVファイルを解析する。
     *
     * @param ShiptUploadRequest $request
     * @return void
     */
    public function parse(ShiptUploadRequest $request) {
        $data = $request->input(GC::DATA);
        $records = $this->service->parse($data);
        if ($this->service->hasError()) {
            throw new CsvInvalidRowException($this->service->getError());
        }

        return response(CsvOrderResource::collection($records), Response::HTTP_CREATED);
    }
}
