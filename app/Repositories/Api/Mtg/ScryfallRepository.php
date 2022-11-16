<?php
namespace App\Repositories\Api\Mtg;
use App\Factory\GuzzleClientFactory;
use Illuminate\Http\Response;

/**
 * scryfall.comのAPI呼び出しクラス
 */
class ScryfallRepository {

    /**
     * 略称から該当エキスパンションを取得する。
     *
     * @param string $attr 略称
     * @return array エキスパンション情報 
     */
    public function getExpansion(string $attr) {
        if (str_ends_with($attr, '_BF') || str_ends_with($attr, '_BLK') ) {
            $attr = substr($attr, 0, 3);
        }
        $client = GuzzleClientFactory::create("scryfall");
        $res = $client->request("GET", 'sets/'.$attr, ['http_errors' => false]);
        if ($res->getStatusCode() == Response::HTTP_NOT_FOUND) {
            return ['released_at' => ''];
        }
        $contents = $res->getBody()->getContents();
        return json_decode($contents, true);
    }

    public function getImageByMultiverseId($id)
    {
        $client = GuzzleClientFactory::create('scryfall');
        $response = $client->request('GET', 'cards/multiverse/'.$id);
        $contents = $response->getBody()->getContents();
        $json = json_decode($contents, true);
        return $json['image_uris']['normal'];
    }
}
?>