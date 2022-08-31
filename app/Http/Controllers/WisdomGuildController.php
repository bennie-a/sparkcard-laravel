<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DomDocument;
use DomXpath;
use App\Models\Card;
class WisdomGuildController extends Controller
{
    public function index() {
        // セラの模範のみ取ってくる。
        $url = 'http://whisper.wisdom-guild.net/search.php?name=Serra Paragon';
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
        $href = $xpath->query('//div[@id="main"]/div[@id="contents"]/div[@class="card"]/b/a')->item(0);
        $url = $href->getAttribute("href");
        $cardname = $href->nodeValue;

        $card = new Card($cardname, $url);
        return view('index', ['card' => $card]);
    }
}
