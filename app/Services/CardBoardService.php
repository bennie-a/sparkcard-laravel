<?php
namespace App\Services;

use App\Enum\CardLanguage;
use App\Enum\ShiptMethod;
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
use App\Services\Constant\NotionConstant as JA;
use App\Services\Constant\NotionStatus;
use FiveamCode\LaravelNotionApi\NotionFacade;

/**
 * Notionの販売管理ボードに関するServiceクラス
 */
class CardBoardService {
    
    private $repo;
    public function __construct() {
        $this->repo = new CardBoardRepository();
    }

    // Statusに一致したカード情報を取得する。
    public function findByStatus($details) {
        $pages = $this->repo->findByStatus($details);
        return $this->toNotionCardList(collect($pages));
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
            $price = $page->getProperty(JA::PRICE);
            $card->setPrice($price->getNumber());
            $card->setExpansion($this->getExpansion($page));

            $lang = $page->getProperty(JA::LANG);
            $card->setLang($lang->getName());

            $stock = $page->getProperty(JA::QTY);
            $card->setStock($stock->getNumber());

            $buyer = $page->getProperty(JA::BUYER);
            $card->setBuyer($buyer->getPlainText());

            $shipping = $page->getProperty(JA::SHIPT_DATE);
            $card->setShippingDate($shipping->getStart());
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
            $price = $page->getProperty(JA::PRICE);
            $cardIndex = $page->getProperty(JA::NUMBER);
            $color = $page->getProperty(JA::COLOR);
            $stock = $page->getProperty(JA::QTY);
            $url = $page->getProperty(JA::IMAGE);
            $lang = $page->getProperty(JA::LANG);
            $condition = $page->getProperty(JA::CONDITION);
            $properties = $array[JA::RAW];
            $descArray = $properties[JA::DESC][JA::RICH];
            $ennameArray = $properties[JA::EN_NAME][JA::RICH];
            $card = new NotionCard();
            $card->setId($page->getId());
            $card->setName($page->getTitle());
            $isFoilProperty = $page->getProperty(JA::FOIL);
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
                $card->setColor(JA::UNDEFINED);
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
                $card->setDesc($descArray[0][JA::PLAIN]);
            }
            if(!empty($ennameArray)) {
                $card->setEnname($ennameArray[0][JA::PLAIN]);
            }

            $card->setFoil($isFoil);
            array_push($resultList, $card);
        }
        return $resultList;

    }

    private function getExpansion($page) {
        $exp = $page->getProperty(JA::SET);
        $exp_id = $exp->getContent()[0]['id'];
        $expModel = Expansion::findByNotionId($exp_id);
        if (!is_null($expModel)) {
            return ['name' => $expModel['name'], 'attr' => $expModel['attr']];
        } else {
            return ['name' => JA::UNDEFINED, 'attr' => 'undefined'];
        }
    }

    public function exists($sparkcardId) {
        $page = $this->repo->findBySparkcardId($sparkcardId);
        return !empty($page);
    }
    
    // 入力値をNotionに登録する。
    public function store(CardInfo $info, array $details) {
        try {
            $page = new Page();
            logger()->debug($info->name);
            $duplicated = $this->repo->findBySparkcardId($info->id);
            $priceVal = intval($details[Header::MARKET_PRICE]);
            if (!empty($duplicated) && $duplicated->getProperty(JA::STATUS)->getName() !== NotionStatus::Complete->value) {
                $stock = $duplicated->getProperty(JA::QTY)->getNumber() + intval($details[Header::QUANTITY]);
                $page->set(JA::QTY, Number::value($stock));
                $page->setId($duplicated->getId());
                $this->updatePage($page);
            } else {
                $page->setTitle(JA::NAME, $info->name);
                $page->setText(JA::EN_NAME, $info->en_name);
                $this->setStatus($page, NotionStatus::PhotoPending);
                $page->setNumber(JA::QTY, $details[Header::QUANTITY]);
                $page->setNumber(JA::NUMBER, $info->number);
                $language = CardLanguage::find($details[Header::LANGUAGE]);
                $page->setSelect(JA::LANG, $language->text());
                $page->setCheckbox(JA::FOIL, $info->isFoil);

                $color = MainColor::find($info->color_id);
                $page->setSelect(JA::COLOR, $color->name);
                $page->setSelect(JA::CONDITION, $details[Header::CONDITION]);
                if (!empty($info->image_url)) {
                    $page->setUrl(JA::IMAGE, $info->image_url);
                }
                $page->setRelation(JA::SET, [$info->exp_id]);
                $page->setRelation(JA::FORM,['e411d9c6acce4e82988230a12668e78d']);
                // 発送方法
                $method = ShiptMethod::findByPrice($priceVal);

                $page->setRelation(JA::SHIPT, [$method->notion_id]);
                $page->set(JA::SPARK_ID, Number::value($info->id));
                $page->set(JA::PRICE, Number::value($priceVal));

                /**
                 * PHP Intelephenseに型通知
                 * @var Page
                 */
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

    public function updateQty($cardId, $quantity) {
        $targetPage = $this->repo->findBySparkcardId($cardId);
        if (is_null($targetPage)) {
            return;
        }
        $page = new Page();
        $page->setId($targetPage->getId());
        $page->setNumber(JA::QTY, $quantity);
        $this->updatePage($page);
    }

    /**
     * 在庫数を減らす。
     * 在庫数が0ならStatusを「削除対象」に変更する。
     *
     * @param integer $id
     * @param integer $quantity
     * @return void
     */
    public function decreaseQuantity(int $id, int $quantity) {
        $targetPage = $this->repo->findBySparkcardId($id);
        if (is_null($targetPage)) {
            logger()->info('Notion card is not found. ID:'.$id);
            return;
        }
        $page = new Page();
        $page->setId($targetPage->getId());
        $beforeQty = $targetPage->getProperty(JA::QTY);
        $afterQty = $beforeQty->getNumber() - $quantity;
        if ($afterQty <= 0) {
            $afterQty = 0;
            $this->setStatus($page, NotionStatus::Archive);
            $page->setNumber(JA::SPARK_ID, 0);
        }

        $page->setNumber(JA::QTY, $afterQty);
        $this->updatePage($page);
    }

    private function setStatus(Page $page, NotionStatus $status) {
        $page->setSelect(JA::STATUS, $status->value);
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
        $pages = $this->repo->findByOrderId($orderId);
        return $pages;
    }

    public function deleteByExp($name) {
        // $pages = $this->repo->findByExp($name);
        // foreach($pages as $p) {
        //     $this->repo->delete($p->getId());
        // }
    }
}