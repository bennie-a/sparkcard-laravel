<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DomDocument;
use DomXpath;
use App\Models\Card;
class WisdomGuildController extends Controller
{
        // URLからHTMLを取得する。
    private static function fetchHtml($url) {
        $method = "GET";

        //接続
        $client = new Client([
            'verify' => config_path().'/cacert.pem'
        ]);

        $response = $client->request($method, $url);

        $posts = $response->getBody()->getContents();
        $dom = new DomDocument();
        libxml_use_internal_errors( true );
        $dom->loadHTML($posts);
        $xpath = new DomXPath($dom);

        return $xpath;
    }

    public function index() {
        // DMUの黒カードのみ取ってくる。
        $url = 'http://whisper.wisdom-guild.net/search.php?&color%5B%5D=black&color_multi=able&set%5B%5D=DMU&sort=eidcid';
        $xpath = self::fetchHtml($url);
        $cardlist = array();

        $hreflist = $xpath->query('//div[@id="main"]/div[@id="contents"]/div[@class="card"]');
        foreach($hreflist as $index => $a) {
            $href = $xpath->query('//b/a')->item($index);
            $url = $href->getAttribute("href");
            $cardname = $href->nodeValue;
            $card = new Card($index, $cardname, $url);
            array_push($cardlist, $card);
        }
        $card = $cardlist[0];
        $xpath = self::fetchHtml($card->getUrl());
        // $main = $xpath->query('//div[@class="padding-vertical"]/table[1]/tr/td[@valign="top"]/table');
        $price = $xpath->query('//div[@class="wg-wonder-price-summary margin-right"]/div[@class="contents"]/b');
        var_dump($price->item(0)->nodeValue);
        return view('index', ['cardlist' => $cardlist]);
    }
}
