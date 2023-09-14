<?php

namespace App\Services;

use App\Enum\CardColor;
use App\Services\WisdomGuildRepository;
use App\Models\Card;
class WisdomGuildService {

    public function __construct() {
        $this->repo = new WisdomGuildRepository();
    }

    /**
     * 検索条件に合ったカード情報を取得する。
     * 
     *@deprecated 2.0.0
     * @param [type] $query
     * @return void
     */
    public function fetch($query) {
        $color = CardColor::matchByString($query['color']);
        $param = [ 
            'query' => [
                'set' => [$query['set']],
                'sort'=> 'eidcid',
                'page'=> 1,
                'color'=>$color->color(),
                'color_multi' => $color->colorMulti(),
                'color_ope' => $color->colorOpe(),
                'cardtype' => $color->cardtype(),
                'cardtype_ope' => $color->cardtypeOpe()
            ]
        ];

        logger()->debug("検索条件", $param);
        $firstXpath = $this->repo->getAll($param);
        $xpathList = [$firstXpath];
        $cardlist = array();

        // ページ番号を取得
        $pagingNodeList = $firstXpath->query('//*[@id="contents"]/div[1]/ul/li[not(@class="now")]/a[not(text()="次へ")]/@href');
        foreach($pagingNodeList as $href) {
            $queries = [];
            $parse = parse_url($href->nodeValue)['query'];
            parse_str($parse, $queries);
            $param['query']['page'] = intval($queries['page']);
            $xpath = $this->repo->getAll($param);
            array_push($xpathList, $xpath);
        };
        $devService = new MtgDevService();
        foreach($xpathList as $xpath) {
            $hreflist = $xpath->query('//*[@id="contents"]/div[@class="card"]/b/a');
            // 1ページ当たりのカードリンク数
            $count = $hreflist->count();
            foreach($hreflist as $a) {
                $url = $a->attributes->getNamedItem("href")->nodeValue;
                // カード名
                $cardname = $a->nodeValue;
                $detailxpath = $this->repo->getCard($url);
                // 価格
                $pricePath = $detailxpath->query('//div[@class="wg-wonder-price-summary margin-right"]/div[@class="contents"]/b');
                if ($pricePath->length > 0) {
                    $price = $pricePath->item(0)->nodeValue;
                }
                $card = new Card($cardname, $price);
                logger()->info('Card Info Get:'.$card->getName());

                // MTG Developers.ioから取得
                $res = $devService->getCardInfo($card->getEnname(), $query['set']);
                if (!empty($res)) {
                    $card->setImageUrl($res["image"]);
                    $card->setIndex($res["id"]);
                    $card->setColor($res["color"]);
                }
                array_push($cardlist, $card);
            }
        }
        return $cardlist;
    }

    
    /**
     * 英語名から日本語名を取得する。
     *
     * @param string $enname 英語名
     * @return string 日本語名
     */
    public function getJpName($enname) {
        $name = $this->getInfo($enname, '//h1/text()');
        $split = explode('/', $name, 2);
        return current($split);
    }

    /** 
     * 英語名からカードの平均価格を取得する。
     */
    public function getPrice($enname) {
        $price = $this->getInfo($enname, '//div[@class="contents"]/b/text()');
        if (empty($price)) {
            $price = 1;
        }
        return $price;
    }

    /**
     * 特定のカード情報からXPathに該当する情報を取得する。
     *
     * @param string $enname
     * @param string $xpath
     * @return string 
     */
    private function getInfo($enname, $xpath) {
        $dom = $this->repo->getSpecificCard($enname);
        $target = $dom->query($xpath);
        if ($target->length == 0) {
            return '';
        }
        return $target->item(0)->nodeValue;
    }
}