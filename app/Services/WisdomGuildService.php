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
    public function fetch($query) {
    $param = [ 
        'query' => [
            'set' => [$query['set']],
            'sort'=> 'eidcid',
            'page'=> 1
        ]
    ];

        $firstXpath = $this->repo->getAll($param);
        $xpathList = [$firstXpath];
        $cardlist = array();

        // ページ番号を取得
        $pagingNodeList = $firstXpath->query('//*[@id="contents"]/div[1]/ul/li[not(@class="now")]/a[not(text()="次へ")]/@href');
        foreach($pagingNodeList as $index => $href) {
            $queries = [];
            $parse = parse_url($href->nodeValue)['query'];
            parse_str($parse, $queries);
            $param['query']['page'] = intval($queries['page']);
            $xpath = $this->repo->getAll($param);
            array_push($xpathList, $xpath);
        };
        // 公式ギャラリー取得クラス
        $gallary = new CardGallaryRepository('dominaria-united');

        foreach($xpathList as $xpathIndex => $xpath) {
            $hreflist = $xpath->query('//*[@id="contents"]/div[@class="card"]/b/a');
            // 1ページ当たりのカードリンク数
            $count = $hreflist->count();
            foreach($hreflist as $index => $a) {
                $url = $a->attributes->getNamedItem("href")->nodeValue;
                // カード名
                $cardname = $a->nodeValue;
                $detailxpath = $this->repo->getCard($url);
                // 価格
                $pricePath = $detailxpath->query('//div[@class="wg-wonder-price-summary margin-right"]/div[@class="contents"]/b');
                if ($pricePath->length > 0) {
                    $price = $pricePath->item(0)->nodeValue;
                }
                // カード番号
                $cardIndex = $xpathIndex * $count + $index;
                $card = new Card($cardIndex, $cardname, $price);
                logger()->info('Card Info Get:'.$card->getName());
                // カードギャラリーのバグ対応
                if (strcmp($card->getName(), "残忍な巡礼者、コー追われのエラス") == 0) {
                    continue;
                }
                // $color = $gallary->getCardColor($card->getName());
                // if ($color == "") {
                //     continue;
                // }
                // $imageurl = $gallary->getImageUrl($card->getName());
                // $card->setColor($color);
                // $card->setImageUrl($imageurl);

                array_push($cardlist, $card);
            }
        }
        return $cardlist;

    }
}