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
        $id = $details['id'];
        $name = $details['name'];
        $isExist = Expansion::isExist($name);
        if ($isExist) {
            return response($details['attr'].' is duplicate', Response::HTTP_BAD_REQUEST);
        }
        $exp = new Expansion();
        $baseId = null;
        if (array_key_exists('base_id', $details)) {
            $baseId = $details['base_id']; 
        }
        $releaseDate = new Carbon($details['release_date']);
        $exp->create(['notion_id' => $id,
        'base_id' => $baseId,
        'name' => $name,
        'attr' => $details['attr'],
        'release_date' => $releaseDate]);
        return response($details['attr'].' insert ok', Response::HTTP_CREATED);
    }
}
