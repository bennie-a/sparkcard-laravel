<?php
namespace App\Services;

use App\Libs\MtgJsonUtil;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Services\ScryfallService;
use App\Services\WisdomGuildService;
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
        $condition = ['card_info.color_id' => $details['color'],
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
    public function post($exp, $details)
    {
        $promotype = $details['promotype'] != '' ? "≪".$details['promotype']."≫": '';
        $name = $details['name'].$promotype;
        $number = $details['number'];
        $isFoil = $details['isFoil'];
        // カード名、エキスパンション略称、カード番号で一意性チェック
        $condition = ['card_info.name' => $name, 'card_info.number' => $number,
                        'expansion.attr' => $exp->attr, 'card_info.isFoil' => $isFoil];
        $cardList = CardInfo::fetchByCondition($condition);
        // 画像URL取得
        $url = $this->service->getImageUrl($details);
        if (count($cardList) == 0) {
            logger()->info('insert card:', ['カード名' => $name, '通常/Foil' => $isFoil]);
            $record = [
                'exp_id'=> $exp->notion_id,
                'name' => $name,
                'barcode' => $this->barcode(),
                'en_name' => $details['en_name'],
                'color_id' => $details['color'],
                'number' => $details['number'],
                'image_url' => $url,
                'isFoil' => $isFoil,
                'language' => $details['language']
            ];
            CardInfo::create($record);
        } else if (!is_null($url)) {
            logger()->info('update card:', ['カード名' => $name, '通常/Foil' => $isFoil]);
            $info = $cardList[0];
            $info->image_url = $url;
            $info->update();
        }
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