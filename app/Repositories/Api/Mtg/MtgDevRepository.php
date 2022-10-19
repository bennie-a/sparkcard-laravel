<?php
namespace App\Repositories\Api\Mtg;
use App\Factory\GuzzleClientFactory;
/**
 * MTG Developers(https://docs.magicthegathering.io/)のAPIクラス
 */
class MtgDevRepository {
/*
 *    カード名(日本語)とエキスパンションからカード情報を取得する。
 */
    public function getCard(string $name, string $exp) {
            $param = [ 
                'query' => [
                    'name' => $name,
                    'set'=> $exp,
                ]
            ];
        $client = GuzzleClientFactory::create("mtgdev");
        $response = $client->request("GET", "cards", $param);
        $contents = $response->getBody()->getContents();
        return json_decode($contents, true);
    }
}
?>
