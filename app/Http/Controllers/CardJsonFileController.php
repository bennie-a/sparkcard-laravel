<?php
namespace App\Http\Controllers;

use App\Exceptions\api\BadRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CardFileRequest;
use App\Services\CardJsonFileService;
use App\Services\Constant\StockpileHeader;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CardJsonFileController extends Controller
{
    private $service;
    public function __construct(CardJsonFileService $service)
    {
        ini_set("max_execution_time",240); // タイムアウトを240秒にセット
        ini_set("max_input_time",240); // パース時間を240秒にセット

        $this->service = $service;
    }
    /**
     * MTGJSONからダウンロードしたJSONファイルを
     * card_infoテーブルへの登録に必要なデータに変換する。
     *
     * @param Request $request
     * @return void
     */
    public function uploadCardFile(CardFileRequest $request) {
        logger()->info('upload start');
        $json = $request->input("data");
        $setcode = $request->input(StockpileHeader::SETCODE);
        // エキスパンション登録チェック
        if (\App\Facades\ExService::isExistByAttr($setcode) == false) {
            throw new BadRequestException('messages.setcode-notFound');
        }
        
        $color = $request->color;
        logger()->debug('色フィルター', [$color]);
        $isDraft = $request->isDraft;
        logger()->debug('通常版フィルタースイッチ', [$isDraft]);
        $data = $this->service->build($setcode, $json, $isDraft, $color);

        logger()->info('upload end');
        return response($data, Response::HTTP_CREATED);

    }
}
?>