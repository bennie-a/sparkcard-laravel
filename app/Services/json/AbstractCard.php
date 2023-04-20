<?php
namespace app\Services\json;

use App\Libs\MtgJsonUtil;
use App\Services\interfaces\CardInfoInterface;
use App\Services\WisdomGuildService;

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

    protected function getJson() {
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
        return $this->json['number'];
    }

    public function language():string {
        return 'JP';
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
        return $this->json['identifiers'];
    }
 }
?>