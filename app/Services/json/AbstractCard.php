<?php
namespace App\Services\json;

use App\Enum\CardColor;
use App\Facades\WisdomGuild;
use App\Factory\SpCardDetectorFactory;
use App\Libs\MtgJsonUtil;
use App\Models\CardInfo;
use App\Models\Foiltype;
use App\Services\Constant\CardConstant as Con;
use App\Services\interfaces\CardInfoInterface;
use Closure;
use GuzzleHttp\Exception\ServerException;

use function PHPUnit\Framework\isEmpty;

abstract class AbstractCard implements CardInfoInterface {
    public function __construct($json)
    {
        $this->json = $json;
        $this->jp = $this->getJpData($json);
    }

    const PROMOTYPE = 'promoTypes';

    const FRAME_EFFECTS = 'frameEffects';

    const IS_FULLART = 'isFullArt';

    const IS_TEXTLESS = 'isTextless';

    protected $json;

    protected $jp;

    protected $color;
    /**
     * foreignDataオブジェクトから日本語部分を取得する。
     *
     * @param [type] $json
     * @return void
     */
    protected function getJpData($json) {
        if (!MtgJsonUtil::hasKey('foreignData', $json)) {
            return;
        }
        $forgienData = $json['foreignData'];
        $filterd = array_filter($forgienData, function($data) {
            return strcmp($data['language'], 'Japanese') == 0;
        });
        return current($filterd);
    }

    public function getJson() {
        return $this->json;
    }

    /**
     * Wisdom Guild.netよりカード名(日本語名)を取得する。
     *
     * @param string $enname 英語名
     * @return string カード名(日本語名)
     */
    protected function getJpnameByAPI($enname) {
        $jpname = '';
        try {
            $jpname = WisdomGuild::getJpName($enname);

        } catch(ServerException $e) {
            logger()->error("日本語名の取得に失敗しました。英語名:{$enname},
                        番号:{$this->number()}, ステータスコード:{$e->getCode()}");
        }
        return $jpname;
    }

    public function jpname(string $enname):string
    {
        if (!empty($this->jp)) {
            return $this->jp[Con::NAME];
        }
        $info = CardInfo::findJpName($enname);
        if (!empty($info)) {
            return $info->name;
        }
        return '';
    }

    public function multiverseId()
    {
        return 0;
    }

    public function scryfallId()
    {
        return '';
    }

    public function number()
    {
        return $this->getJson()['number'];
    }

    public function language():string {
        return 'JP';
    }

    public function setcode():string {
        return $this->getJson()['setCode'];
    }

    public function promotype() {

        $booster = 'boosterfun';
        if ($this->isTextless()) {
            return 'textless';
        }
        if ($this->isFullArt()) {
            return 'fullart';
        }
        if (!$this->hasPromotype()) {
            return 'draft';
        }
        $filterd = function($f) {
            $excluded = ['confettifoil'];
            return !in_array($f, $excluded);
        };
        $promo = $this->filtering($this->promotypeKey(), $filterd);
        if ($promo != $booster) {
            return $promo;
        }

        // boosterfunの場合はframe_effectsを取得する。
        $frame = $this->frameEffects();
        $detector = SpCardDetectorFactory::create($this->getJson()["setCode"]);
        if ($frame != $booster) {
            $frame = $detector->showcase($frame, $this);
            return $frame;
        }

        $border = $this->borderColor();
        if ($border == Con::BORDERLESS) {
            return $detector->borderless($this);
        }

        $othercase = $detector->othercase($this);
        if(!empty($othercase)) {
            return $othercase;
        }
        return $booster;
    }

    public function specialFoil() {
        if (!$this->isSpecialFoil() || $this->isPromo() || !$this->hasPromotype()) {
            return 'foil';
        }
        $filterd = function($f) {
            return $f != 'boosterfun' && $f != 'bundle';
        };
        $type = $this->filtering($this->promotypeKey(), $filterd);
        if (empty($type) || $type == 'oilslick') {
            return 'foil';
        }
        return $type;
    }

    /**
     * isTextlessの項目を取得する。
     *
     * @return boolean
     */
    public function isTextless() {
        return MtgJsonUtil::isTrue(self::IS_TEXTLESS, $this->getJson());
    }

    public function frameEffects() {
        if (!MtgJsonUtil::hasKey(self::FRAME_EFFECTS, $this->json)) {
            return 'boosterfun';
        }
        $filterd = function($f) {
            return $f != 'legendary' && $f != 'etched' && $f != 'inverted';
        };
        $filterd = $this->filtering(self::FRAME_EFFECTS, $filterd);
        if ($filterd == false) {
            return 'boosterfun';
        }
        return $filterd;
    }

    /**
     * カードの枠のバージョンを取得する。
     *
     * @return string
     */
    public function frameVersion() {
        return $this->getJson()['frameVersion'];
    }

    public function borderColor() {
        return $this->getJson()['borderColor'];
    }

    private function filtering($keyword, Closure $filterd) {
        $frames = $this->getJson()[$keyword];
        $filterd = array_filter($frames, $filterd);
        return current($filterd);
    }

    public function finishes() {
        return $this->getJson()["finishes"];
    }

    /**
     * 特殊Foilが存在する判定する。
     *
     * @return boolean
     */
    public function isSpecialFoil() {
        $finishes = $this->finishes();
        return count($finishes) == 1 && in_array('foil', $finishes);
    }

    /**
     * イベント用カードかどうか判別する
     *
     * @return boolean
     */
    public function isPromo() {
        return MtgJsonUtil::isTrue("isPromo", $this->getJson());
    }

    /**
     * 除外したいカード情報の条件式
     *
     * @return boolean 除外したい場合はtrue, そうでない場合はfalse
     */
    public function isExclude() {
        return false;
    }

    public function color() {
        if (empty($this->color)) {
            $type = CardColor::match($this->json);
            $this->color = $type->value;
        }
        return $this->color;
    }

    /**
     *  JSONファイルの"types"の値を取得する。
     *
     * @return array
     */
    public function types() {
        return $this->getJson()["types"];
    }

    /**
     * JSONファイルの"subtypes"の値を取得する。
     *
     * @return array
     */
    public function subtypes() {
        return $this->getJson()["subtypes"];
    }

    /**
     * 英語版のmultiverse_idを返す。
     *
     * @return int
     */
    protected function getEnMultiverseId() {
        $id = $this->getIdentifiersValue(Con::MULTIVERSEID);
        return empty($id) ? 0 : $id;
    }

    protected function getEnScryfallId () {
        return $this->getIdentifiersValue(Con::SCRYFALLID);
    }
    private function getIdentifiersValue($key){
        $identifiers = $this->getIdentifiers();
        return MtgJsonUtil::hasKey($key, $identifiers) ? $identifiers[$key] : '';
    }

    protected function getIdentifiers() {
        return $this->getJson()[Con::IDENTIFIERS];
    }

    /**
     * カードがフルアート版かどうか判定する。
     *
     * @return boolean
     */
    public function isFullArt() {
        $frameEffects = $this->frameEffects();
        return $frameEffects === Con::FULLART;
    }

    protected function hasPromotype() {
        return MtgJsonUtil::hasKey(self::PROMOTYPE, $this->getJson());
    }

    protected function promotypeKey() : string {
        return self::PROMOTYPE;
    }

    public function foiltype() {
        $foiltype = [];
        if($this->isSpecialFoil()) {
            $type = $this->specialFoil();
            $typename = $this->findFoilName($type);
            array_push($foiltype, $typename);
        } else {
            $finishes = $this->finishes();
            $foiltype = array_map(function($f) {
                if ($f == 'nonfoil') {
                    return '通常版';
                } else if ($f== 'foil') {
                    return 'Foil';
                } else {
                    $typename = $this->findFoilName($f);
                    return $typename;
                }
                }, $finishes);
        }
        return $foiltype;
    }

    private function findFoilName(string $attr) {
        $result = Foiltype::findByAttr($attr);
        $typename = empty($result) ? '不明':$result->name;
        return $typename;
    }

 }
?>
