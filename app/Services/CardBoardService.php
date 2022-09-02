<?php
namespace App\Services;
use App\Repositories\Api\Notion\CardBoardRepository;
use App\Repositories\Api\Notion\ExpansionRepository;
use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Exceptions\NotionException;
use Illuminate\Http\Response;

class CardBoardService {
    
    public function __construct() {
        $this->repo = new CardBoardRepository();
    }

    // 入力値をNotionに登録する。
    public function store(array $details) {
        $page = new Page();
        $page->setTitle("名前", $details['name']);
        $page->setText("英名", $details['enname']);
        $page->setSelect("Status", "ロジクラ要登録");
        $page->setNumber("枚数", 0);
        $page->setNumber("価格", $details['price']);
        $page->setNumber("カード番号", $details['index']);
        $page->setSelect("言語", "日本語");
        $page->setCheckbox("Foil", false);
        // $page->setSelect("色", "無色");
        $expansion = new ExpansionRepository();
        logger()->debug($details['attr']);
        $exp = $expansion->findByAttr($details['attr']);
        $page->setRelation("エキスパンション",[$exp]);
        $page->setRelation("プラットフォーム",['864fb4c2af7641e5aa4daaaafbf97f51']);
        try {
            $page = $this->repo->store($page);
            // ページID
            logger()->info($page->getId());
            response($page->getId(), Response::HTTP_OK);
        } catch (NotionException $e) {
            logger()->error($e->getMessage());
        }

    }
}