<?php
namespace App\Services;

use App\Libs\MtgJsonUtil;
use App\Repositories\Api\Mtg\ScryfallRepository;
use app\Services\json\AbstractCard;
use App\Enum\CardColor;
use App\Exceptions\ConflictException;
use App\Exceptions\NotFoundException;
use App\Factory\CardInfoFactory;
use App\Services\json\ScryfallCard;
use Illuminate\Http\Response;

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
        if (MtgJsonUtil::hasKey('imageurl', $details)) {
            return $details['imageurl'];
        }
        $multiverseId = $details['multiverseId'];
        $json = [];
        if (!empty($multiverseId)) {
            $json = $this->repo->getCardByMultiverseId($multiverseId);
        } else if (MtgJsonUtil::hasKey('scryfallId',$details) && !empty($details['scryfallId'])) {
            $scryfallId = $details['scryfallId'];
            $json = $this->repo->getCardByScryFallId($scryfallId);
        } else {
            return null;
        }
        $layout = $json['layout'];
        $images = [];
        if ($layout == 'transform') {
            // 両面カードの場合は表面のカードを取得する。
            $faces = current($json['card_faces']);
            $images = $faces['image_uris'];
        } else {
            $images = $json['image_uris'];
        }
        return $images['png'];
    }

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
     * @return void
     */
    public function getCardInfoByNumber(array $details) {
        $setcode = $details["setcode"];
        $number = $details["number"];
        $language = $details["language"];
        $contents = $this->repo->getCardInfoByNumber($setcode, $number, $language);
        // $card = CardInfoFactory::create($contents);
        return $this->toArray($contents);
    }

    protected function toArray(array $contents) {
        $card = new ScryfallCard($contents);
        $color = CardColor::findColor($card->colors(), $card->cardtype());
        $promotype = \Promo::find($card);
        logger()->info($promotype);
        return ['name' => $card->name(), 
                    'multiverse_id' => $card->multiverseId(),
                    'en_name' => $card->enname(),
                    'color' => $color->value,
                    'promotype'=>$promotype,
                    'imageurl' => $card->imageurl(),
                    'number' => $card->number(),
                    'setcode' => $card->setcode(),
                    'reprint' => $card->reprint()
            ];
    }

}
?>