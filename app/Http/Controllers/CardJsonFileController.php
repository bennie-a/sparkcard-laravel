<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
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
    public function uploadCardFile(Request $request) {
        $rules = ['data' => 'required'];
        $messages = [
            'data.required' => 'JSONファイルにdataオブジェクトがありません'];
        Validator::make($request->all(), $rules, $messages)->validate();
        $json = $request->input("data");
        // if (!array_key_exists("setCode", $json)) {
        //     return response("JSONファイルにsetCodeがありません。", Response::HTTP_UNPROCESSABLE_ENTITY);
        // }
        // if (!array_key_exists("cards", $json)) {
        //     return response("JSONファイルにcardsがありません。", Response::HTTP_UNPROCESSABLE_ENTITY);
        // }
        $cards = $json["cards"];
        $setcode = $json["code"];
        logger()->debug($setcode);
        $data = $this->service->build($setcode, $cards);
        // foreach($cards as $c) {
        //     logger()->debug($c["name"]);
        // }

        return response($data, Response::HTTP_CREATED);
    }
}
?>