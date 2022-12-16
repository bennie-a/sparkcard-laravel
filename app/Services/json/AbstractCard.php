<?php
namespace app\Services\json;
use App\Services\interface\CardInfoInterface;
use App\Services\WisdomGuildService;

 abstract class AbstractCard implements CardInfoInterface {
    public function __construct($json)
    {
        $this->json = $json;
    }

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
        return $this->jp["name"];        
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
 }
?>