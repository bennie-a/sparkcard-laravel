<?php
namespace App\Services;
use App\Repositories\Api\Mtg\MtgDevRepository;
use Illuminate\Support\Facades\Log;
use Nette\Utils\Arrays;
use stdClass;

use function Psy\debug;

class MtgDevService {
    public function __construct() {
        $this->repo = new MtgDevRepository();
    }

    /**
     * カード名(日本語)とエキスパンションに該当するカード情報を取得する。
     */
    public function getCardInfo($name, $exp) {
        $res = $this->repo->getCard($name, $exp);
        $card = $res["cards"];
        $foreigns = $card[0]["foreignNames"];
        $target = $this->extractCardByLang($foreigns, "Japanese");
        return $target;
    }

    private function extractCardByLang($array, string $language) {
        $target = array_filter($array, function($card) use ($language) {
            return strcmp($card['language'], $language) == 0;
        });
        $keys = array_keys($target);
        return $target[$keys[0]];
    }
}
?>
