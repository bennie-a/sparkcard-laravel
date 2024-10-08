<?php
namespace Tests\Unit\Read;

use Tests\Unit\CardJsonFileTest;
use App\Services\Constant\CardConstant as Con;

/**
 * 『ダスクモーン：戦慄の館』のテストケース
 */
class DskJsonFileTest extends CardJsonFileTest {
    const BORDERLESS_NAME = 'ボーダレス';
    public function specialdataprovider() {
        $json = 'dsk.json';
        return [
            '「二重露光」ショーケース' => [$json, [Con::NUMBER => '352', Con::PROMOTYPE => '「二重露光」ショーケース']],
            '「超常」フレーム' => [$json, [Con::NUMBER => '302', Con::PROMOTYPE => '「超常」フレーム']],
            '「鏡の怪物」ボーダレス' => [$json, [Con::NUMBER => '348', Con::PROMOTYPE => '「鏡の怪物」ボーダレス']],
            'ボーダレスサイクル土地' => [$json, [Con::NUMBER => '331', Con::PROMOTYPE => self::BORDERLESS_NAME]],
            'ボーダレス「部屋」カード' => [$json, [Con::NUMBER => '335', Con::PROMOTYPE => self::BORDERLESS_NAME]],
            '通常版魁渡' => [$json, [Con::NUMBER => '220', Con::PROMOTYPE => '']],
            'ボーダレス版魁渡' => [$json, [Con::NUMBER => '328', Con::PROMOTYPE => self::BORDERLESS_NAME]],
            '二重露光版魁渡' => [$json, [Con::NUMBER => '354', Con::PROMOTYPE => '「二重露光」ショーケース']],
        ];
    }

    protected function filtering_card(array $result, array $expected) {
        $filterd = array_filter($result, function($a) use($expected){
            if ($a[Con::NUMBER] == $expected[Con::NUMBER] ) {
                return $a;
            }
        });
        $actualcard = current($filterd);
        return $actualcard;
    }

}