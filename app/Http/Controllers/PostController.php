<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DomDocument;
class PostController extends Controller
{
    public function index() {
        $url = 'http://whisper.wisdom-guild.net/card/Academy+Wall/';

        $tag_id = "laravel";

        // $url = "https://qiita.com/api/v2/tags/" . $tag_id . "/items?page=1&per_page=20";
        $method = "GET";

        //接続
        $client = new Client([
    'verify' => 'C:\Users\salto\OneDrive\ドキュメント\GitHub\sparkcard-laravel\config\cacert.pem'
]);

        $response = $client->request($method, $url);

        $posts = $response->getBody()->getContents();
        $domDocument = new DomDocument();
        libxml_use_internal_errors( true );
        $domDocument->loadHTML($posts);
        $h1 = $domDocument->getElementsByTagName('h1')->item(0);
        var_dump($h1->textContent);
        $posts = json_decode($posts, true);
        // // 日本語のみ抽出。
        // $filtered = $posts->filter(function($element) {
        //     return $element->language == 'Japanese';
        // });
        return view('index', ['posts' => $posts]);
    }
}
