<?php

namespace App\Http\Controllers\Notion;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notion\ExpansionResource;
use App\Services\ExpansionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * NotionのエキスパンションAPI
 */
class ExpansionController extends Controller
{
    private $service;

    public function __construct(ExpansionService $service)
    {
        $this->service = $service;
    }
    /**
     * エキスパンション一覧を取得する。
     */
    public function index(Request $request) {
        $result = $this->service->findAll();
        $json = ExpansionResource::collection($result);
        return response()->json($json, Response::HTTP_OK);
    }

    /**
     * エキスパンションを1件Notionに登録する。 
     * 
     * */
    public function store(Request $request) {
        $details = $request->only(['attr', 'name', 'format']);
        $this->service->store($details);
    }
}
?>
