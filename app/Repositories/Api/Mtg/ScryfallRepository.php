<?php
namespace App\Repositories\Api\Mtg;
use App\Factory\GuzzleClientFactory;
use App\Libs\MtgJsonUtil;
use GuzzleHttp\Exception\ClientException;
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
        try {
            if (str_ends_with($attr, '_BF') || str_ends_with($attr, '_BLK') ) {
                $attr = substr($attr, 0, 3);
            }
            $client = GuzzleClientFactory::create("scryfall");
            $res = $client->request("GET", 'sets/'.$attr, ['http_errors' => false]);
            if ($res->getStatusCode() == Response::HTTP_NOT_FOUND) {
                return ['released_at' => ''];
            }
            return $this->getContents($res);
        } catch (ClientException $e) {
            return [];
        }
    }

    /**
     * multiverseidからカード情報を取得する。
     *
     * @param string $id multiverseid
     * @return array
     */
    public function getCardByMultiverseId($id)
    {
        $client = GuzzleClientFactory::create('scryfall');
        $response = $client->request('GET', 'cards/multiverse/'.$id);
        return $this->getContents($response);
    }

    /**
     * scryfallidからカード情報を取得する。
     *
     * @param string $id scryfallid
     * @return array
     */
    public function getCardByScryFallId($id)
    {
        $client = GuzzleClientFactory::create('scryfall');
        $response = $client->request('GET', 'cards/'.$id);
        return $this->getContents($response);
    }

    /**
     * セット略称とカード番号から情報を取得する。
     *
     * @param string $setCode
     * @param integer $number
     * @return array
     */
    public function getCardInfoByNumber(string $setCode, int $number, string $language) {
        $client = $this->client();
        $rowerCode = \mb_strtolower($setCode);
        $response = $client->request('GET', 'cards/'.$rowerCode.'/'.$number.'/'.$language);
        return $this->getContents($response);
    }

    public function getCardInfoByName(string $setcode, string $name) {
        try {
            $param = [ 
                'query' => [
                    'exact' => $name,
                    'set'=> strtolower($setcode)
                ]
            ];
            $response = $this->client()->request('GET', 'cards/named', $param);
            return $this->getContents($response);
        } catch(ClientException $e) {
            logger()->info('Not Found in Scryfall');
            return [];
        }
    }

    /**
     * レスポンスからJSON情報を取得する。
     *
     * @param Response $response
     * @return array JSON情報
     */
    private function getContents($response) {
        $contents = $response->getBody()->getContents();
        return json_decode($contents, true);
    }

    private function client() {
        return GuzzleClientFactory::create('scryfall');
    }
}
?>