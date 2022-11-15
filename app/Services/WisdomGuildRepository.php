<?php
namespace App\Services;
use App\Factory\GuzzleClientFactory;
use DOMDocument;
use DOMXPath;
/**
 * Wisdom Guild.netのAPIクラス
 */
class WisdomGuildRepository {
    // public function getAll(): Collection;
    public function getAll($param) {
        $client = $this->create();
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

    /**
     * 英語名から特定のカード情報を取得する。
     *
     * @param string $enname
     * @return DOMXPath
     */
    public function getSpecificCard(string $enname) {
        $client = $this->create();
        $response = $client->request('GET', '/card/'.$enname);
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

    private function create() {
        return GuzzleClientFactory::create('wisdom');
    }
}