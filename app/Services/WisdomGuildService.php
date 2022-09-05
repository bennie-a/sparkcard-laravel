<?php

namespace App\Services;
use App\Services\ApiWisdomGuildRepository;
use DomDocument;
use DomXpath;
use App\Models\Card;
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
        $pagingNodeList = $xpath->query('//*[@id="contents"]/div[1]/ul/li[not(@class="now")]/a[not(text()="次へ")]/@href');
        $pagings = [];
        foreach($pagingNodeList as $index => $href) {
            $queries = [];
            $parse = parse_url($href->nodeValue)['query'];
            parse_str($parse, $queries);
            array_push($pagings, $queries['page']);
        };
        $hreflist = $xpath->query('//*[@id="contents"]/div[@class="card"]/b/a');
        foreach($hreflist as $index => $a) {
            $url = $a->attributes->getNamedItem("href")->nodeValue;
            $cardname = $a->nodeValue;

            $detailxpath = $this->repo->getCard($url);
            $pricePath = $detailxpath->query('//div[@class="wg-wonder-price-summary margin-right"]/div[@class="contents"]/b');
            $price = $pricePath->item(0)->nodeValue;
            $card = new Card($index, $cardname, $price);
            array_push($cardlist, $card);
        }
        return $cardlist;

    }
}