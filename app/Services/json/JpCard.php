<?php
namespace App\Services\json;

class JpCard extends AbstractCard
{
    public function __construct($json)
    {
        parent::__construct($json);
        $this->jp = $this->getJp($json);
    }

    public function build(string $enname, $json)
    {
        $result = [];
        $forgienData = $json['foreignData'];
        $filterd = array_filter($forgienData, function($data) {
            return strcmp($data['language'], 'Japanese') == 0;
        });
        $jp = current($filterd);
        $result = ['name' => $jp->name, 'enname' => $enname];
        if (array_key_exists('multiverseId', $jp)) {
            $result['multiverseId'] = $jp['multiverseId'];
        }
        return $result;
    }

    // public function jpname(string $enname) {
    //     return $this->jp["name"];        
    // }

}
?>