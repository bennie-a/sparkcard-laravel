<?php

namespace App\Http\Controllers\DB;

use App\Http\Controllers\Controller;
use App\Http\Requests\CardInfoDBRequest;
use App\Models\Expansion;
use App\Services\CardInfoDBService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use function PHPUnit\Framework\isNull;

class CardInfoDBController extends Controller
{
    public function __construct(CardInfoDBService $service)
    {
        $this->service = $service;
    }
    /**
     * card_infoテーブルにデータを1件登録する。
     *
     * @param Request $request
     * @return void
     */
    public function store(CardInfoDBRequest $request)
    {
        $details = $request->all();
        $setCode = $details['setCode'];
        $exp = Expansion::where('attr', $setCode)->get();
        if ($exp->count() == 0) {
            return response($setCode.'がDBに登録されていません', 422);
        }
        // card_infoテーブルに登録
        $this->service->post($exp[0], $details);
        return response('', Response::HTTP_CREATED);
    }
}
