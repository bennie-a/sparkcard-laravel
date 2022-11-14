<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Requests\CardFileRequest;
use App\Services\CardJsonFileService;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CardJsonFileController extends Controller
{
    public function __construct(CardJsonFileService $service)
    {
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
        $json = $request->input("data");
        $data = $this->service->build($json);

        return response($data, Response::HTTP_CREATED);
    }
}
?>