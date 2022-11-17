<?php
namespace App\Services;

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
        $condition = ['card_info.color_id' => $details['color'], 'expansion.attr' => $details['set']];
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
        // カード名、エキスパンション略称、カード番号で一意性チェック
        $condition = ['card_info.name' => $name, 'card_info.number' => $number, 'expansion.attr' => $exp->attr];
        $cardList = CardInfo::fetchByCondition($condition);
        // 画像URL取得
        $url = $this->getImageUrl($details);
        if (count($cardList) == 0) {
            logger()->info('insert row:', [$name]);
            $record = [
                'exp_id'=> $exp->notion_id,
                'name' => $name,
                'barcode' => $this->barcode(),
                'en_name' => $details['en_name'],
                'color_id' => $details['color'],
                'number' => $details['number'],
                'image_url' => $url
            ];
            CardInfo::create($record);
        } else {
            logger()->info('already exists in card_info:'.$name);
        }
        // 1.画像URLがある⇒スルー
        // 2.画像URLがない⇒
        // ・multiverseId or scryfallIDがある⇒画像URLを取得して更新
        // ・multiverseIdもscryfallIDもない⇒スルー
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
    /**
     * 画像URLを取得する。
     *
     * @param string $multiverseId
     * @param string $scryfallId
     * @return string 画像URL(multiverseIdもscryfallIdの無かったらNULL)
     */
    public function getImageUrl($details)
    {
        $multiverseId = $details['multiverseId'];
        $scryfallId = $details['scryfallId'];
        if (!empty($multiverseId)) {
            return $this->service->getImageByMultiverseId($multiverseId);
        } else if (!empty($scryfallId)) {
            return $this->service->getImageByScryFallId($scryfallId);
        }

        return null;
    }
}
?>