<?php

namespace App\Http\Controllers\DB;

use App\Http\Controllers\Controller;
use App\Models\Expansion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * DBのエキスパンション用コントローラークラス
 */
class ExpDBController extends Controller
{
    /**
     * expansionテーブルにデータを1件登録する。
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request) {
        $details = $request->all();
        $exp = new Expansion();
        $releaseDate = new Carbon($details['release_date']);
        $exp->create(['notion_id' => $details['id'],
        'base_id' => $details['base_id'],
        'name' => $details['name'],
        'attr' => $details['attr'],
        'release_date' => $releaseDate]);
        return response($details['attr'].' insert ok', Response::HTTP_CREATED);
    }
}
