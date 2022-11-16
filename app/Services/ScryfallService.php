<?php
namespace App\Services;
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

    public function getImageByMultiverseId($id)
    {
        return $this->repo->getImageByMultiverseId($id);
    }
    public function getImageByScryFallId($id)
    {
        return $this->repo->getImageByScryFallId($id);
    }
}
?>