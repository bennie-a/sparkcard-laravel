<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
class PostController extends Controller
{
    public function index() {
        $url = 'https://api.magicthegathering.io/v1/cards/386616';

        $tag_id = "laravel";

        // $url = "https://qiita.com/api/v2/tags/" . $tag_id . "/items?page=1&per_page=20";
        $method = "GET";

        //接続
        $client = new Client([
    'verify' => 'C:\Users\salto\OneDrive\ドキュメント\GitHub\sparkcard-laravel\config\cacert.pem'
]);

        $response = $client->request($method, $url);

        $posts = $response->getBody()->getContents();
        $posts = json_decode($posts, true);
        var_dump($posts);
        return view('index', ['posts' => $posts]);
    }
}
