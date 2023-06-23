<?php
namespace App\Services;

use App\Enum\CardColor;
use App\Exceptions\NotFoundException;
use App\Http\Response\CustomResponse;
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
        $cardArray = $res["cards"];
        // 0件対応
        if (empty($cardArray)) {
            throw new NotFoundException(CustomResponse::HTTP_NOT_FOUND_CARD, $name.'がAPIにありません。');
        }
        $card = current($res["cards"]);
        $color = CardColor::match($card);
        $foreigns = $card["foreignNames"];
        $target = $this->extractCardByLang($foreigns, "Japanese");
        $responseArray['id'] = $target["multiverseid"];
        $responseArray['color'] = $color->text();
        $responseArray['image'] = $target["imageUrl"];
        return $responseArray;
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
        return $target[current($keys)];
    }
}
?>
