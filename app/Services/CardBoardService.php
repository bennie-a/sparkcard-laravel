<?php
namespace App\Services;

use App\Models\Card;
use App\Models\notion\NotionCard;
use App\Repositories\Api\Notion\CardBoardRepository;
use App\Repositories\Api\Notion\ExpansionRepository;
use FiveamCode\LaravelNotionApi\Entities\Collections\PageCollection;
use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Exceptions\NotionException;
use FiveamCode\LaravelNotionApi\Query\Filters\Filter;
use FiveamCode\LaravelNotionApi\Query\Filters\Operators;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class CardBoardService {
    
    public function __construct() {
        $this->repo = new CardBoardRepository();
    }

    public function findByStatus($status) {
        $pages = $this->repo->findByStatus($status);
        $resultList = array();
        if (count($pages) == 0) {
            $error = ['code' => 1000, 'message'=>'not found'];
            return $error;
        }
        foreach($pages as $page) {
            $array = $page->toArray();
            $price = $page->getProperty('価格');
            $cardarray = ['id'=> $array['id'], 'name' => $array['title'], 'price'=> $price->getContent()];
            array_push($resultList, $cardarray);
        }
        return $resultList;
    }
    
    // 入力値をNotionに登録する。
    public function store(array $details) {
        $page = new Page();
        $page->setTitle("名前", $details['name']);
        $page->setText("英名", $details['enname']);
        $page->setSelect("Status", "ロジクラ要登録");
        $page->setNumber("枚数", 0);
        $priceVal = intval($details['price']);
        $page->setNumber("価格", $priceVal);
        $page->setNumber("カード番号", $details['index']);
        $page->setSelect("言語", "日本語");
        $page->setCheckbox("Foil", false);
        $page->setSelect("色", $details['color']);
        $page->setSelect("状態", "NM");
        $page->setUrl('画像URL', $details['imageurl']);
        $expansion = new ExpansionRepository();
        logger()->debug($details['attr']);
        $exp = $expansion->findByAttr($details['attr']);
        $page->setRelation("エキスパンション",[$exp]);
        $page->setRelation("プラットフォーム",['864fb4c2af7641e5aa4daaaafbf97f51']);
        // ミニレター
        $sends = [];
        if ($priceVal >= 1500) {
            // クリックポスト
            array_push($sends, '29c8c95ed21645909cafea172a5dd2f7');
        } else {
            array_push($sends, 'e7db5d1cf759498fb66bac08644885da');
        }
        $page->setRelation('発送方法', $sends);
        try {
            $page = $this->repo->store($page);
            // ページID
            logger()->info($page->getId());
            response($page->getId(), Response::HTTP_OK);
        } catch (NotionException $e) {
            logger()->error($e->getMessage());
        }

    }

    public function deleteByExp($name) {
        $pages = $this->repo->findByExp($name);
        foreach($pages as $p) {
            $this->repo->delete($p->getId());
        }
    }
}