<?php

namespace App\Http\Controllers\DB;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostExRequest;
use App\Http\Resources\ExpDBResource;
use App\Http\Resources\Notion\ExpansionResource;
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
        return response()->json($list);
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
