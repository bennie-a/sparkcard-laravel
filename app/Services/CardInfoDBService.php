<?php
namespace App\Services;

use App\Exceptions\api\NoContentException;
use App\Exceptions\api\NoExpException;
use App\Exceptions\api\NoFoilTypeException;
use App\Exceptions\api\NoPromoTypeException;
use App\Exceptions\NotFoundException;
use App\Http\Response\CustomResponse;
use App\Models\CardInfo;
use App\Models\Expansion;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Facades\ScryfallServ;
use App\Facades\WisdomGuild;
use App\Models\Foiltype;
use App\Models\Promotype;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant;
use Illuminate\Database\Eloquent\Collection;

/**
 * card_infoテーブルのロジッククラス
 */
class CardInfoDBService {

    /**
     * Undocumented function
     *
     * @param [type] $details
     * @return Collection
     */
    public function fetch($details)
    {
        $condition = [
                    'card_info.name' => $details[Con::NAME],
                    'card_info.color_id' => $details[Con::COLOR],
                    'e.attr' => $details[Con::SET],
                    'card_info.isFoil' => $details[Con::IS_FOIL]];
        $list = CardInfo::fetchByCondition($condition);

        foreach ($list as $info) {
            $price = WisdomGuild::getPrice($info[Con::EN_NAME]);
            $info[Con::PRICE] = $price;
        }

        return $list;
    }

    /**
     * card_infoテーブルにデータを1件登録する。
     *
     * @param Expansion $exp エキスパンション
     * @param array $details Requestで受信したjsonデータ
     * @return void
     */
    public function post($setCode, $details)
    {
        $name = $details[GlobalConstant::NAME];
        $foiltype = $details[Con::FOIL_TYPE];
        $exp = Expansion::where('attr', $setCode)->first();
        if (\is_null($exp)) {
            throw new NoExpException($setCode);
        }
        $hasPromo = Promotype::isExist($exp->notion_id, $details[Con::PROMO_ID]);
        if ($hasPromo === false) {
            throw new NoPromoTypeException($details[Con::PROMO_ID]);
        }
        $number = $details[Con::NUMBER];
        foreach($foiltype as $foil) {
            $isFoil = $foil != '通常版';
            logger()->info("import start.");
            // カード名、エキスパンション略称、カード番号で一意性チェック
            $foiltype = Foiltype::findByName($foil);
            if (empty($foiltype)) {
                throw new NoFoilTypeException($name, $number);
            }

            $info = CardInfo::getCardinfo($exp->notion_id, $number, $foiltype->id);
            // 画像URL取得
            $url = ScryfallServ::getImageUrl($details);
            $log = ['カード名' => $name, 'number' => $details['number'], 'カード仕様' => $foil];
            if (empty($info)) {
                logger()->info("insert card.",$log);
                $record = [
                    'exp_id'=> $exp->notion_id,
                     GlobalConstant::NAME => $name,
                    'barcode' => $this->barcode(),
                    'en_name' => $details['en_name'],
                    'color_id' => $details['color'],
                    Con::NUMBER => $details[Con::NUMBER],
                    'image_url' => $url,
                    'isFoil' => $isFoil,
                    Con::FOIL_ID => $foiltype->id,
                    Con::PROMO_ID => $details[Con::PROMO_ID]
                ];
                $record = CardInfo::create($record);
            } else if (!is_null($url) && boolval($details['isSkip']) !== true) {
                logger()->info('update card:', $log);
                $info->image_url = $url;
                $info->name = $name;
                $info->promotype_id = $details[Con::PROMO_ID];
                $info->update();
            } else {
                logger()->info('skip card:', ['カード名' => $name, '通常/Foil' => $isFoil]);
            }
            logger()->info("import end.");
        };
    }

    
    /**
     * Scryfallからカード情報を取得して、DBに登録する。
     *
     * @param [type] $setcode セット略称
     * @param [type] $cardname カード名(英語)
     * @return void
     */
    public function postByScryfall($setcode, string $name, string $enname, $isFoil) {
        logger()->info('Retrieve info from Scryfall', [$setcode, $name]);
        $details = ScryfallServ::getCardInfoByName($setcode, $enname);
        $details['name'] = $name;
        $details['isFoil'] = $isFoil;
        logger()->info('Post Info', [$details['setcode'], $name]);
        $record = $this->post($setcode, $details);
        return $record;
    }

    /**
     * ランダム16桁のバーコードを取得する。
     * @return string 生成した16桁のバーコード 
     */
    private function barcode()
    {
        // str_shuffle関数
        $barcode = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz0123456789"), 0, 16);
        return $barcode;
    }
}
?>