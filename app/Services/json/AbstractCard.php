<?php
namespace app\Services\json;

use App\Libs\MtgJsonUtil;
use App\Services\interfaces\CardInfoInterface;
use App\Services\WisdomGuildService;
use Closure;

abstract class AbstractCard implements CardInfoInterface {
    public function __construct($json)
    {
        $this->json = $json;
    }

    const NAME = 'name';

    const EN_NAME = 'en_name';

    const MULTIVERSEID = 'multiverseId';

    const IDENTIFER = 'identifiers';

    const SCRYFALLID = 'scryfallId';

    const PROMOTYPE = 'promoTypes';

    const FRAME_EFFECTS = 'frameEffects';

    const IS_FULLART = 'isFullArt';

    const IS_TEXTLESS = 'isTextless';
    /**
     * foreignDataオブジェクトから日本語部分を取得する。
     *
     * @param [type] $json
     * @return void 
     */
    protected function getJp($json) {
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
        $service = new WisdomGuildService();
        $jpname = $service->getJpName($enname);
        return $jpname;
    }

    public function jpname(string $enname):string {
        return $this->jp[self::NAME];        
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
            return $f != 'textured';
        };
        $promo = $this->filtering($this->promotypeKey(), $filterd);
        if ($promo != $booster) {
            return $promo;
        }
        
        // boosterfanの場合はframeeffectを取得する。
        $frame = $this->frameEffects();
        if ($frame != $booster) {
            return $frame;
        }        
        $border = $this->getJson()['borderColor'];
        if ($border == 'borderless') {
            return $border;
        }
        return $booster;
    }

    public function specialFoil() {
        if (!$this->isSpecialFoil() || !$this->hasPromotype()) {
            return 'foil';
        }
        $filterd = function($f) {
            return $f != 'boosterfun';
        };
        $type = $this->filtering($this->promotypeKey(), $filterd);
        if ($type == 'oilslick') {
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
        return count($finishes) == 1 && in_array("foil", $finishes);
    }

    /**
     * 除外したいカード情報の条件式
     *
     * @return boolean 除外したい場合はtrue, そうでない場合はfalse
     */
    public function isExclude($json, array $cardInfo) {
        return false;
    }

    protected function getEnMultiverseId() {
        return $this->getIdentifiersValue(self::MULTIVERSEID);
    }

    protected function getEnScryfallId () {
        return $this->getIdentifiersValue(self::SCRYFALLID);
    }
    private function getIdentifiersValue($key){
        $identifiers = $this->getIdentifiers();
        return MtgJsonUtil::hasKey($key, $identifiers) ? $identifiers[$key] : '';
    }

    protected function getIdentifiers() {
        return $this->getJson()['identifiers'];
    }

    protected function isFullArt() {
        return MtgJsonUtil::hasKey(self::IS_FULLART, $this->json);
    }

    protected function hasPromotype() {
        return MtgJsonUtil::hasKey(self::PROMOTYPE, $this->json);
    }

    protected function promotypeKey() : string {
        return self::PROMOTYPE;
    }
 }
?>