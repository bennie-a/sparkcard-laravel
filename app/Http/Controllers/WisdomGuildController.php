<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DomDocument;
use DomXpath;
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
        $cardnode = $xpath->query('//div[@id="main"]/div[@id="contents"]/div[@class="card"]/b');
        // var_dump($cardnode->count());
        $cardname = $cardnode->item(0)->nodeValue;
        $posts = json_decode($posts, true);
        return view('index', ['cardname' => $cardname]);
    }
}
