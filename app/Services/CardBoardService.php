<?php
namespace App\Services;

use App\Enum\CardLanguage;
use App\Factory\NotionPageFactory;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\MainColor;
use App\Models\notion\NotionCard;
use App\Repositories\Api\Notion\CardBoardRepository;
use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Entities\Properties\Number;
use FiveamCode\LaravelNotionApi\Exceptions\NotionException;
use App\Services\Constant\StockpileHeader as Header;
use Illuminate\Support\Collection;

/**
 * Notionの販売管理ボードに関するServiceクラス
 */
class CardBoardService {
    
    private const JA_QTY = '枚数';

    private const JA_PRICE = '価格';

    private const JA_SET = 'エキスパンション';

    private const JA_LANG = '言語';

    private $repo;
    public function __construct() {
        $this->repo = new CardBoardRepository();
    }

    // Statusに一致したカード情報を取得する。
    public function findByStatus($details) {
        $pages = $this->repo->findByStatus($details);
        return $this->toNotionCardList($pages);
    }

    /**
     * 「やよい青色申告」に該当するカードを取得する。
     *
     * @param boolean $isMercari trueならショップがメルカリShopsのみ
     * @param boolean $isBase trueならショップがBASEショップのみ
     * @return array
     */
    public function findByTaxStatus(bool $isMercari, bool $isBase) {
        $pages = $this->repo->findByTaxStatus($isMercari, $isBase);
        $resultList = array();
        if (count($pages) == 0) {
            return $resultList;
        }
        foreach($pages as $page) {
            $card = new NotionCard();
            $card->setName($page->getTitle());
            $price = $page->getProperty($this::JA_PRICE);
            $card->setPrice($price->getNumber());
            $card->setExpansion($this->getExpansion($page));
            $lang = $page->getProperty($this::JA_LANG);
            $card->setLang($lang->getName());

            $buyer = $page->getProperty("購入者名");
            $card->setBuyer($buyer->getPlainText());

            $shipping = $page->getProperty("発送日");
            $card->setShippingDate($shipping->getStart());

            $orderNo = $page->getProperty("注文番号");
            $card->setOrderNo($orderNo->getPlainText());
            array_push($resultList, $card);
        }
        return $resultList;
    }

    /**
     * Notionから取得した情報をCollectionオブジェクトに変換する。
     *(中身はNotionCardオブジェクトの配列)
     * @param Collection $pages
     * @return array $resultList
     */
    private function toNotionCardList(Collection $pages) {
        $resultList = array();
        if (count($pages) == 0) {
            $error = ['status' => 204, 'message'=>'件数は0件です。'];
            return $error;
        }
        foreach($pages as $page) {
            $array = $page->toArray();
            $price = $page->getProperty($this::JA_PRICE);
            $cardIndex = $page->getProperty('カード番号');
            $color = $page->getProperty('色');
            $stock = $page->getProperty($this::JA_QTY);
            $url = $page->getProperty('画像URL');
            $lang = $page->getProperty($this::JA_LANG);
            $condition = $page->getProperty('状態');
            $properties = $array['rawProperties'];
            $descArray = $properties['説明文']['rich_text'];
            $ennameArray = $properties['英名']['rich_text'];
            $card = new NotionCard();
            $card->setId($page->getId());
            $card->setName($page->getTitle());
            $isFoilProperty = $page->getProperty('Foil');
            $isFoil = $isFoilProperty->getContent();
            $exp = $this->getExpansion($page);
            $card->setExpansion($exp);
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
                $card->setStock(0);
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

            $card->setFoil($isFoil);
            array_push($resultList, $card);
        }
        return $resultList;

    }

    private function getExpansion($page) {
        $exp = $page->getProperty($this::JA_SET);
        $exp_id = $exp->getContent()[0]['id'];
        $expModel = Expansion::findByNotionId($exp_id);
        if (!is_null($expModel)) {
            return ['name' => $expModel['name'], 'attr' => $expModel['attr']];
        } else {
            return ['name' => '不明', 'attr' => 'undefined'];
        }
    }
    
    // 入力値をNotionに登録する。
    public function store(CardInfo $info, array $details) {
        try {
            $page = new Page();
            logger()->debug($info->name);
            $duplicated = $this->repo->findBySparkcardId($info->id);
            $priceVal = intval($details[Header::MARKET_PRICE]);
            $page->set($this::JA_PRICE, Number::value($priceVal));
            if (!empty($duplicated)) {
                $stock = $duplicated->getProperty("枚数")->getNumber() + intval($details[Header::QUANTITY]);
                $page->set("枚数", Number::value($stock));
                $page->setId($duplicated->getId());
                $this->updatePage($page);
            } else {
                $page->setTitle("名前", $info->name);
                $page->setText("英名", $info->en_name);
                $page->setSelect("Status", "要写真撮影");
                $page->setNumber("枚数", $details[Header::QUANTITY]);
                $page->setNumber("カード番号", $info->number);
                $language = CardLanguage::find($details[Header::LANGUAGE]);
                $page->setSelect("言語", $language->text());
                $page->setCheckbox("Foil", $info->isFoil);

                $color = MainColor::find($info->color_id);
                $page->setSelect("色", $color->name);
                $page->setSelect("状態", $details[Header::CONDITION]);
                if (!empty($info->image_url)) {
                    $page->setUrl('画像URL', $info->image_url);
                }
                // $expansion = Expansion::where('attr', $details['attr'])->first();
                $page->setRelation($this::JA_SET, [$info->exp_id]);
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
                $page->set('sparkcard_id', Number::value($info->id));
                $page = $this->repo->store($page);
                // ページID
                logger()->info($page->getId());
            }
        } catch (NotionException $e) {
            logger()->error($e->getMessage());
        }

    }

    public function update($id, $details) {
        $factory = new NotionPageFactory();
        $page = $factory->create($id, $details);
        $this->updatePage($page);
    }

    public function updatePage(Page $page) {
        try {
            $this->repo->update($page);
        } catch(NotionException $e) {
            throw $e;
        }
    }

    
    /**
     * 注文番号に該当する販売カードを取得する。
     *
     * @param string $spcid
     * @return void
     */
    public function findByOrderId(string $orderId) {
        return $this->repo->findByOrderId($orderId);
    }

    public function deleteByExp($name) {
        // $pages = $this->repo->findByExp($name);
        // foreach($pages as $p) {
        //     $this->repo->delete($p->getId());
        // }
    }
}