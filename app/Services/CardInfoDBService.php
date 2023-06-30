<?php
namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Http\Response\CustomResponse;
use App\Libs\MtgJsonUtil;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Services\ScryfallService;
use App\Services\WisdomGuildService;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Facades\ScryfallServ;
use App\Enum\CardColor;
use Illuminate\Http\Response;

/**
 * card_infoテーブルのロジッククラス
 */
class CardInfoDBService {
    public function __construct()
    {
        $this->service = new ScryfallService();
    }

    public function fetch($details)
    {
        $condition = [
                    'card_info.name' => $details['name'],
                    'card_info.color_id' => $details['color'],
                    'expansion.attr' => $details['set'],
                    'card_info.isFoil' => $details['isFoil']];
        $list = CardInfo::fetchByCondition($condition);
        $service = new WisdomGuildService();

        foreach ($list as $info) {
            $price = $service->getPrice($info['en_name']);
            $info['price'] = $price;
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
        $promotype = $details['promotype'] != '' ? "≪".$details['promotype']."≫": '';
        $name = $details['name'].$promotype;
        $isFoil = $details['isFoil'];
        logger()->info("import start.",['カード名' => $name, '通常/Foil' => $isFoil]);
        $exp = Expansion::where('attr', $setCode)->first();
        if (\is_null($exp)) {
            logger()->error('not exist:'.$setCode);
            throw new HttpResponseException(response($setCode.'がDBに登録されていません', CustomResponse::HTTP_NOT_FOUND_EXPANSION));
        }
        // カード名、エキスパンション略称、カード番号で一意性チェック
        $info = CardInfo::findSpecificCard($exp->notion_id, $name, $isFoil);
        // 画像URL取得
        $url = $this->service->getImageUrl($details);
        if (empty($info)) {
            logger()->info('insert card:', ['カード名' => $name, '通常/Foil' => $isFoil]);
            $record = [
                'exp_id'=> $exp->notion_id,
                'name' => $name,
                'barcode' => $this->barcode(),
                'en_name' => $details['en_name'],
                'color_id' => $details['color'],
                'number' => $details['number'],
                'image_url' => $url,
                'isFoil' => $isFoil
            ];
            $record = CardInfo::create($record);
            return $record;
        } else if (!is_null($url) && boolval($details['isSkip']) !== true) {
            logger()->info('update card:', ['カード名' => $name, '通常/Foil' => $isFoil]);
            $info->image_url = $url;
            $info->update();
        } else {
            logger()->info('skip card:', ['カード名' => $name, '通常/Foil' => $isFoil]);
        }
        logger()->info("import end.");
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
        $details = \ScryfallServ::getCardInfoByName($setcode, $enname);
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