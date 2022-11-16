<?php
namespace App\Services;

use App\Enum\PromoType;
use App\Models\CardInfo;
use App\Models\Expansion;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * card_infoテーブルのロジッククラス
 */
class CardInfoDBService {
    /**
     * card_infoテーブルにデータを1件登録する。
     *
     * @param Expansion $exp エキスパンション
     * @param array $details Requestで受信したjsonデータ
     * @return void
     */
    public function post($exp, $details)
    {
        $name = $details['name'];
        $number = $details['number'];
        // カード名、エキスパンション略称、カード番号で一意性チェック
        $condition = ['card_info.name' => $name, 'card_info.number' => $number, 'expansion.attr' => $exp->attr];
        $cardList = CardInfo::where($condition)->
                        join('expansion', 'expansion.notion_id', '=', 'card_info.exp_id')->get();
        // 画像URL取得
        $url = $this->getImageUrl($details);
        $promotype = $details['promotype'] != '' ? "≪".$details['promotype']."≫": '';
        if ($cardList->count() == 0) {
            logger()->info('新規登録:', [$name]);
            $record = [
                'exp_id'=> $exp->notion_id,
                'name' => $details['name'].$promotype,
                'barcode' => $this->barcode(),
                'en_name' => $details['en_name'],
                'color_id' => $details['color'],
                'number' => $details['number'],
                'image_url' => $url
            ];
            CardInfo::create($record);
        } else {
            throw new Exception();
        }
        // 2.で検索。
        // 無かったら
        // multiverseId or scryfallIDがある⇒画像URLをAPIから取得する。
        // どっちもない⇒画像URLを取得せずに登録する。
        // card_infoテーブルに1件追加。
        // あった場合
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
        // $multiverseId = $details['multiverseId'];
        // $scryfallId = $details['scryfallId'];

        return null;
    }
}
?>