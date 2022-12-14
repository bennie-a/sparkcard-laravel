<?php
namespace App\Services;

use App\Libs\MtgJsonUtil;
use App\Repositories\Api\Mtg\ScryfallRepository;

/**
 * scryfall.comのAPIサービスクラス
 */
class ScryfallService {

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

        /**
     * 画像URLを取得する。
     *
     * @param array $details JSONファイルから読み込んだカード情報1件
     * @return string 画像URL
     */
    public function getImageUrl($details)
    {
        $multiverseId = $details['multiverseId'];
        $scryfallId = $details['scryfallId'];
        $json = [];
        if (!empty($multiverseId)) {
            $json = $this->repo->getCardByMultiverseId($multiverseId);
        } else if (!empty($scryfallId)) {
            $json = $this->repo->getCardByScryFallId($scryfallId);
        } else {
            return null;
        }
        $layout = $json['layout'];
        if ($layout == 'transform') {
            // 両面カードの場合は表面のカードを取得する。
            $json = current($json['card_faces']);
        }
        $images = $json['image_uris'];
        return $images['png'];
    }
}
?>