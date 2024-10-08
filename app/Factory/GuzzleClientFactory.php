<?php

namespace App\Factory;
use GuzzleHttp\Client;

use Illuminate\Support\Arr;
class GuzzleClientFactory {
    public static function create($path) {
        $urlMap = [
            'wisdom' => 'http://whisper.wisdom-guild.net',
            'mtgdev' => 'https://api.magicthegathering.io/v1/',
            'scryfall' => 'https://api.scryfall.com/',
            'base' => 'https://api.thebase.in/'
        ];
        if (!Arr::exists($urlMap, $path)) {
          throw new \Exception('invalid path');
        }
        $url = Arr::get($urlMap, $path);
        $client = self::createByUrl($url);
        return $client;
    }

    public static function createByUrl($url) {
        $client = new Client(['base_uri' => $url, 'allow_redirects' => ['track_redirects' => true]]);
        return $client;
    }
}