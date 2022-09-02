<?php

namespace App\Http\Controllers\Notion;

use App\Http\Controllers\Controller;
use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Exceptions\NotionException;
use FiveamCode\LaravelNotionApi\Notion;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

use function Psy\debug;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $token = config("notion.token");
        $notion = new Notion($token);
        $testbaseId = config("notion.database");
        logger()->debug($testbaseId);
        try {
            $database = $notion->databases()->find($testbaseId);
            logger()->info($database->getTitle());
            response($database->getTitle(), Response::HTTP_OK);
        } catch(NotionException $e) {
            logger()->error($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $token = config("notion.token");
        $notion = new Notion($token);
        $testbaseId = config("notion.database");

        $page = new Page();
        $page->setTitle("名前", "生けるレガシー、カーン");

        $page->setSelect("ステータス", "ロジクラ要登録");
        $page->setNumber("枚数", 1);
        $page->setNumber("価格", 900);
        $page->setSelect("言語", "日本語");
        $page->setCheckbox("Foil", false);
        $page->setSelect("色", "無色");
        $page->setRelation("エキスパンション", ['e72e96b424574e9682a6264d7e7731ec']);
        try {
            $page = $notion->pages()->createInDatabase($testbaseId, $page);
            // ページID
            logger()->info($page->getId());
            response($page->getId(), Response::HTTP_OK);
        } catch (NotionException $e) {
            logger()->error($e->getMessage());
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
