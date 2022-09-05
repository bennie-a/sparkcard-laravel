<?php

namespace App\Services;
use App\Services\WisdomGuildRepository;
use DomDocument;
use DomXpath;
use App\Models\Card;
use App\Repositories\Api\Mtg\CardGallaryRepository;
use DOMNode;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class WisdomGuildService {

    public function __construct() {
        $this->repo = new WisdomGuildRepository();
    }



    // 要ページ対応。
    public function fetch() {
        $xpath = $this->repo->getAll(1);
        $cardlist = array();

        // ページ番号を取得
        $pagingNodeList = $xpath->query('//*[@id="contents"]/div[1]/ul/li[not(@class="now")]/a/@href');
        $pagings = [];
        foreach($pagingNodeList as $index => $href) {
            $queries = [];
            $parse = parse_url($href->nodeValue)['query'];
            parse_str($parse, $queries);
            array_push($pagings, $queries['page']);
        };
        $gallary = new CardGallaryRepository('dominaria-united');
        $target = $gallary->getCardColor();
                    foreach($target as $t) {
                logger()->debug($t->nodeValue);
            }


        $hreflist = $xpath->query('//*[@id="contents"]/div[@class="card"]/b/a');
        foreach($hreflist as $index => $a) {
            $url = $a->attributes->getNamedItem("href")->nodeValue;
            // カード名
            $cardname = $a->nodeValue;

            $detailxpath = $this->repo->getCard($url);
            $pricePath = $detailxpath->query('//div[@class="wg-wonder-price-summary margin-right"]/div[@class="contents"]/b');
            $price = $pricePath->item(0)->nodeValue;

            $card = new Card($index, $cardname, $price);
            // 色と画像URLをカードギャラリーから取得
            if ($target == null) {
                logger()->info("No Target".$card->getName());
            }
                // $color = $target->item(0)->nodeValue;
                // logger()->debug($card->getName().":".$color);
                // $card->setColor($color);
            // $color = $info->item(0)->parent->parent->query('h2/span[text()]')->item();
            // logger()->debug($color->nodeValue);
            array_push($cardlist, $card);
        }
        return $cardlist;

    }
}