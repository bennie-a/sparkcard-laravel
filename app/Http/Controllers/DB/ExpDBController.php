<?php

namespace App\Http\Controllers\DB;

use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostExRequest;
use App\Http\Resources\ExpDBResource;
use App\Http\Resources\Notion\ExpansionResource;
use App\Http\Response\CustomResponse;
use App\Models\CardInfo;
use App\Models\Expansion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\ExpansionService;
use Exception;

use function Spatie\Ignition\ErrorPage\report;

/**
 * DBのエキスパンション用コントローラークラス
 */
class ExpDBController extends Controller

{
    public function __construct (ExpansionService $service) {
        $this->service = $service;
    }

    /**
     * 略称と部分一致するエキスパンション名を最大5件取得する。
     *
     * @return エキスパンション情報(ID,名称,略称,BASE_ID,リリース日)
     */
    public function index(Request $request) {
        $query = $request->input("query");
        logger()->info('入力パラメータ', [$query]);
        $list = Expansion::where('attr', 'like', '%'.$query.'%')->limit(5)->get();
        if (count($list) == 0) {
            throw new NotFoundException(CustomResponse::HTTP_NOT_FOUND_EXPANSION, '検索結果がありません。');
        }
        foreach($list as $exp) {
            logger()->debug($exp->name.':'.$exp->notion_id);
            $count = CardInfo::where('exp_id', $exp->notion_id)->count();
            $exp['count'] = $count;
        }
        return response()->json($list, Response::HTTP_OK);
    }

    /**
     * expansionテーブルとNotionのエキスパンション一覧にデータを1件登録する。
     *
     * @param PostExRequest $request
     * @return void
     */
    public function store(PostExRequest $request) {
        $details = ['name' => $request->input('name'),
                         'attr' => $request->input('attr'),
                         'block' => $request->input('block'),
                         'format' => $request->input('format'),
                          'release_date' => $request->input('release_date')];
        // エキスパンション一覧に登録する。
        $this->service->store($details);
        return response($details['attr'].' insert ok', Response::HTTP_CREATED);
    }
}
