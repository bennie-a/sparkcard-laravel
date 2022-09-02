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

            // URLからHTMLを取得する。
    private static function fetchHtml($contents) {
        $dom = new DomDocument();
        libxml_use_internal_errors( true );
        $dom->loadHTML($contents);
        $xpath = new DomXPath($dom);

        return $xpath;
    }

    public function fetch() {
        $contents = $this->repo->getAll();
        $xpath = self::fetchHtml($contents);
        $cardlist = array();

        $hreflist = $xpath->query('//div[@id="main"]/div[@id="contents"]/div[@class="card"]');
        foreach($hreflist as $index => $a) {
            $href = $xpath->query('//b/a')->item($index);
            $url = $href->attributes->getNamedItem("href")->nodeValue;
            $cardname = $href->nodeValue;

            $detailContents = $this->repo->getCard($url);
            $detailxpath = self::fetchHtml($detailContents);
            $pricePath = $detailxpath->query('//div[@class="wg-wonder-price-summary margin-right"]/div[@class="contents"]/b');
            $price = $pricePath->item(0)->nodeValue;
            $card = new Card($index, $cardname, $price);
            array_push($cardlist, $card);
        }
        return $cardlist;

    }
}