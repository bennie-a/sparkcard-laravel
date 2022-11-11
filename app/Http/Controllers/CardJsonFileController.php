<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CardJsonFileController extends Controller
{
    /**
     * MTGJSONからダウンロードしたJSONファイルを
     * card_infoテーブルへの登録に必要なデータに変換する。
     *
     * @param Request $request
     * @return void
     */
    public function uploadCardFile(Request $request) {
        $rules = ['data' => 'required'];
        $messages = ['data.required' => 'JSONにdataオブジェクトがありません'];
        Validator::make($request->all(), $rules, $messages)->validate();
        $json = $request->only("data");
        $cards = $json["cards"];
        foreach($cards as $c) {
            logger()->debug($c["name"]);
        }
        return response('', Response::HTTP_CREATED);
    }
}
?>