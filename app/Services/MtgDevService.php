<?php
namespace App\Services;

use App\Models\mtg\CardColor;
use App\Repositories\Api\Mtg\MtgDevRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Enum;

/**
 * MTG Developer.ioのServiceクラス。
 * 
 */
class MtgDevService {
    public function __construct() {
        $this->repo = new MtgDevRepository();
    }

    /**
     * カード名とエキスパンションに該当するカード情報を取得する。
     * @param string $name カード名(日本語)
     * @param string $exp エキスパンション(略称)
     * @return array 連想配列
     */
    public function getCardInfo($name, $exp) {
        $res = $this->repo->getCard($name, $exp);
        $card = $res["cards"][0];
        // 要無色対応
        $color = CardColor::match($card);
        $foreigns = $card["foreignNames"];
        $target = $this->extractCardByLang($foreigns, "Japanese");
        return ['id' => $target["multiverseid"], 'color' => $color->text(), 'image' => $target["imageUrl"]];
    }

    /**
     * 指定した言語に該当するカード情報を取得する。
     * @param array $array 言語別カード情報
     * @param string $language 指定した言語
     * @return array 連想配列
     */
    private function extractCardByLang($array, string $language) {
        $target = array_filter($array, function($card) use ($language) {
            return strcmp($card['language'], $language) == 0;
        });
        $keys = array_keys($target);
        return $target[$keys[0]];
    }
}
?>
