<?php
namespace App\Services;
use App\Factory\GuzzleClientFactory;
use DOMDocument;
use DOMXPath;

class WisdomGuildRepository {
    // public function getAll(): Collection;
    public function getAll($param) {
        $client = GuzzleClientFactory::create('wisdom');
        $response = $client->request("GET", 'search.php', $param);
        $contents = $response->getBody()->getContents();
        logger()->debug("Page".$param['query']['page']." get");
        return $this->toDomXpath($contents);
    }

    public function getCard($url) {
        $client = GuzzleClientFactory::createByUrl($url);
        $response = $client->request('GET', '');
        $contents = $response->getBody()->getContents();
        return $this->toDomXpath($contents);
    }

    // HTMLをDOMXPathに変換する。
    protected function toDomXpath($contents) {
        $dom = new DOMDocument();
        libxml_use_internal_errors( true );
        $dom->loadHTML($contents);
        $xpath = new DOMXPath($dom);

        return $xpath;
    }
}