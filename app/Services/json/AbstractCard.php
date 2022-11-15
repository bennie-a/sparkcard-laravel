<?php
namespace app\Services\json;
use App\Services\interface\CardInfoInterface;

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
    
    public function jpname(string $enname):string {
        return $this->jp["name"];        
    }
    public function scryfallId()
    {
        return '';
    }

 }
?>