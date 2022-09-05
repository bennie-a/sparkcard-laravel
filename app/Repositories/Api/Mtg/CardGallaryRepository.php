<?php
namespace App\Repositories\Api\Mtg;
use App\Factory\GuzzleClientFactory;
use App\Services\WisdomGuildRepository;

// MTG公式のカードギャラリークラス
class CardGallaryRepository extends WisdomGuildRepository{

    private $expansion;
    public function __construct($expansion)
    {
        $this->expansion = $expansion;
    }

    // カード情報を取得する。
    public function getCardColor() {
        $client = GuzzleClientFactory::create("gallary");
        $response = $client->request('GET', $this->expansion);
        $htmlSource = $response->getBody()->getContents();
        $html = mb_convert_encoding($htmlSource, 'UTF-8', 'HTML-ENTITIES');
        $xpath = $this->toDomXpath($html);
        $target = $xpath->query("//p[@class='rtecenter']");
        
        return $target;
    }

    public function getAllGallary() {
        $client = GuzzleClientFactory::create("gallary");
        $response = $client->request('GET', $this->expansion);
        $htmlSource = $response->getBody()->getContents();
        $html = mb_convert_encoding($htmlSource, 'UTF-8', 'HTML-ENTITIES');
        $xpath = $this->toDomXpath($html);
        return $xpath;
    }
}

