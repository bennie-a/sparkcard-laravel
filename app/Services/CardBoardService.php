<?php
namespace App\Services;

use App\Factory\NotionPageFactory;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\notion\NotionCard;
use App\Repositories\Api\Notion\CardBoardRepository;
use App\Repositories\Api\Notion\ExpansionRepository;
use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Exceptions\NotionException;
use Illuminate\Http\Response;

class CardBoardService {
    
    public function __construct() {
        $this->repo = new CardBoardRepository();
    }

    // Statusに一致したカード情報を取得する。
    public function findByStatus($status, $details) {
        $pages = $this->repo->findByStatus($status, $details);
        $resultList = array();
        if (count($pages) == 0) {
            $error = ['status' => 204, 'message'=>'件数は0件です。'];
            return $error;
        }
        foreach($pages as $page) {
            $array = $page->toArray();
            $price = $page->getProperty('価格');
            $cardIndex = $page->getProperty('カード番号');
            $color = $page->getProperty('色');
            $stock = $page->getProperty('枚数');
            $url = $page->getProperty('画像URL');
            $lang = $page->getProperty('言語');
            $exp = $page->getProperty('エキスパンション');
            $condition = $page->getProperty('状態');
            $properties = $array['rawProperties'];
            $descArray = $properties['説明文']['rich_text'];
            $ennameArray = $properties['英名']['rich_text'];
            $card = new NotionCard();
            $card->setId($array['id']);
            $card->setName($array['title']);

            $exp_id = $exp->getRawContent()[0]['id'];
            $expModel = Expansion::where('notion_id', $exp_id)->first();
            if (!is_null($expModel)) {
                $card->setExpansion(['name' => $expModel['name'], 'attr' => $expModel['attr']]);
                if (!empty($card->getName())) {
                    $cardInfo = CardInfo::findCard($exp_id, $card->getName());
                    if (!empty($cardInfo)) {
                        $card->setBarcode($cardInfo['barcode']);
                        logger()->debug($card->getBarcode());
                    }
                }
            } else {
                $card->setExpansion(['name' => '不明', 'attr' => 'undefined']);
            }
            $card->setPrice($price->getContent());
            if (!is_null($cardIndex)) {
                $card->setIndex($cardIndex->getContent());
            }
            if (!is_null($url)) {
                logger()->debug($card->getName());
                $card->setImageUrl($url->getContent());
            }
            if (!is_null($lang)) {
                $card->setLang($lang->getName());
            }
            if (!is_null($color)) {
                $card->setColor($color->getName());
            } else {
                $card->setColor("不明");
            }
            if (!is_null($stock)) {
                $card->setStock($stock->getContent());
            } else {
                $card->setStock("0");
            }
            if (!is_null($condition)) {
                $card->setCondition($condition->getName());
            }

            if(!empty($descArray)) {
                $card->setDesc($descArray[0]['plain_text']);
            }
            if(!empty($ennameArray)) {
                $card->setEnname($ennameArray[0]['plain_text']);
            }

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
        $page->setNumber("枚数", $details['stock']);
        $priceVal = intval($details['price']);
        $page->setNumber("価格", $priceVal);
        $page->setNumber("カード番号", $details['index']);
        $page->setSelect("言語", "日本語");
        $page->setCheckbox("Foil", false);
        $page->setSelect("色", $details['color']);
        $page->setSelect("状態", "NM");
        if (array_key_exists('imageUrl', $details)) {
            $page->setUrl('画像URL', $details['imageUrl']);
        }
        $expansion = Expansion::where('attr', $details['attr'])->first();
        logger()->debug($details['attr']);
        $page->setRelation("エキスパンション",[$expansion['notion_id']]);
        $page->setRelation("プラットフォーム",['e411d9c6acce4e82988230a12668e78d']);
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