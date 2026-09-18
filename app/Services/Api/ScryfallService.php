<?php
namespace App\Services\Api;
use App\Libs\MtgJsonUtil;
use App\Repositories\Api\Mtg\ScryfallRepository;
use App\Enum\CardColor;
use App\Exceptions\api\NotFoundException;
use App\Factory\CardInfoFactory;
use App\Services\Constant\CardConstant as Con;
use App\Services\json\Scryfall\ScryfallArtCard;
use App\Services\json\Scryfall\ScryfallCard;
use App\Services\json\Scryfall\ScryfallTransformCard;
use Illuminate\Http\Response;

/**
 * scryfall.comのAPIサービスクラス
 */
class ScryfallService {

    private $repo;
    public function __construct() {
        $this->repo = new ScryfallRepository();
    }

    /**
     * 略称からリリース日を取得する。
     *
     * @param string $attr
     * @return void
     */
    public function getReleaseDate(string $attr) {
        $res = $this->repo->getExpansion($attr);
        return $res['released_at'];
    }

    public function findSet(string $attr) {
        $res = $this->repo->getExpansion($attr);
        if (empty($res['released_at'])) {
            throw new NotFoundException(Response::HTTP_NOT_FOUND, 'APIに該当セットなし');
        }
        return $res;
    }

     /**
     * 画像URLを取得する。
     *
     * @param array $details JSONファイルから読み込んだカード情報1件
     * @return string 画像URL
     */
    public function getImageUrl($details)
    {
        $imageUrl = MtgJsonUtil::getIfExists(Con::IMAGE_URL, $details);
        if (!empty($imageUrl)) {
            return $imageUrl;
        }

        $card = $this->findScryfallCard($details);
        return $card->imageurl()['png'];
    }

    /**
     * Scryfall形式のカードカードオブジェクトを取得する。
     *
     * @param array $details
     * @return ScryfallCard | null
     */
    private function findScryfallCard(array $details)
    {
        $json = $this->findCardJson($details);

        if (empty($json)) {
            return null;
        }

        return $this->createScryfallCard($json);
    }

    /**
     * Scryfall.comからJSONデータを取得する。
     *
     * @param array $details
     * @return array
     */
    private function findCardJson(array $details):array
    {
        $multiverseId = MtgJsonUtil::getIfExists(Con::MULTIVERSEID, $details);

        if (!empty($multiverseId)) {
            return $this->repo->getCardByMultiverseId($multiverseId);
        }

        $scryfallId = MtgJsonUtil::getIfExists(
            'scryfallId',
            $details
        );

        if (!empty($scryfallId)) {
            return $this->repo->getCardByScryFallId($scryfallId);
        }

        return [];
    }

    /**
     * Scryfallオブジェクトを生成する。
     *
     * @param array $json
     * @return ScryfallCard
     */
    private function createScryfallCard(array $json):ScryfallCard
    {
        if (in_array($json['layout'], ['transform', 'reversible_card'], true)) {
            return new ScryfallTransformCard($json);
        }

        return new ScryfallCard($json);
    }

    /**
     * @deprecated(version:5.2.2)
     *
     * @param string $setcode
     * @param string $name
     * @return void
     */
    public function getCardInfoByName(string $setcode, string $name) {
        $contents = $this->repo->getCardInfoByName($setcode, $name);
        if (empty($contents)) {
            throw new NotFoundException(Response::HTTP_NOT_FOUND, 'APIに該当カードなし');
        }
        return $this->toArray($contents);
    }
    /**
     * /cards/:code/:numberで情報を取得する。
     *
     * @param array $details
     * @return array
     */
    public function getCardInfoByNumber(array $details) {
        $json = $this->repo->getCardInfoByNumber($details);
        $card = null;
        if ($json['layout'] == 'art_series') {
            $card = new ScryfallArtCard($json);
        } else {
            $card = new ScryfallCard($json);
        }

        return $card;
    }
}
?>
