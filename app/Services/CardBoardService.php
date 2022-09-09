<?php
namespace App\Services;

use App\Factory\NotionPageFactory;
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

    // Statusに一致したカード情報を取得する。
    public function findByStatus($status, $details) {
        $pages = $this->repo->findByStatus($status, $details);
        $resultList = array();
        if (count($pages) == 0) {
            $error = ['status' => 404, 'message'=>'件数は0件です。'];
            return $error;
        }
        $expRepo = new ExpansionRepository();
        foreach($pages as $page) {
            $array = $page->toArray();
            $price = $page->getProperty('価格');
            $cardIndex = $page->getProperty('カード番号');
            $color = $page->getProperty('色');
            $stock = $page->getProperty('枚数');
            $url = $page->getProperty('画像URL');
            $lang = $page->getProperty('言語');
            // $expPage = $expRepo->findByPage($exp);
            $card = new NotionCard();
            // $card->setExpansion($expPage->getTitle());
            $card->setId($array['id']);
            $card->setName($array['title']);
            $card->setPrice($price->getContent());
            if (!is_null($cardIndex)) {
                $card->setIndex($cardIndex->getContent());
            }
            if (!is_null($url)) {
                $card->setImageUrl($url->getContent());
            }
            $card->setLang($lang->getName());
            $card->setColor($color->getName());
            $card->setStock(($stock->getContent()));

            // $enname = $page->getProperty('英名');
            // logger()->debug();
            // $card->setEnname($enname->getContent());
            $isFoil = $page->getProperty('Foil');
            $card->setFoil($isFoil->getContent());
            array_push($resultList, $card);
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

    public function update($id, $details) {
        try {
            $factory = new NotionPageFactory();
            $page = $factory->create($id, $details);
            $this->repo->update($page);
        } catch(NotionException $e) {
            throw $e;
        }
    }

    public function deleteByExp($name) {
        $pages = $this->repo->findByExp($name);
        foreach($pages as $p) {
            $this->repo->delete($p->getId());
        }
    }
}