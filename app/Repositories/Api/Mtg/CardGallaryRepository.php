<?php
namespace App\Repositories\Api\Mtg;
use App\Factory\GuzzleClientFactory;
use App\Services\WisdomGuildRepository;

// MTG公式のカードギャラリークラス
class CardGallaryRepository extends WisdomGuildRepository{

    private $expansion;
    private $xpath;
    public function __construct($expansion)
    {
        $this->expansion = $expansion;
        $this->xpath = $this->getAllGallary();
    }

    public function getAllGallary() {
        $client = GuzzleClientFactory::create("gallary");
        $response = $client->request('GET', $this->expansion);
        $htmlSource = $response->getBody()->getContents();
        $html = mb_convert_encoding($htmlSource, 'UTF-8', 'HTML-ENTITIES');
        $xpath = $this->toDomXpath($html);
        return $xpath;
    }

    // 指定したカードの色を取得する。
    public function getCardColor($name) {
        $nameQuery = "//p[contains(text(), '".$name."')]";
        $colorNode = $this->xpath->query($nameQuery."/../../h2/span");
        if ($colorNode == null) {
            logger()->error('Not Found Color:'.$name);
            return "";
        } else {
            return $colorNode->item(0)->nodeValue;
        }
    }

    // 指定したカード名から画像URLを取得する。
    public function getImageUrl($name) {
        $nameQuery = "//p[contains(text(), '".$name."')]";
        $imgNode = $this->xpath->query($nameQuery."/img/@src");
        return  $imgNode->item(0)->nodeValue;
    }
}

