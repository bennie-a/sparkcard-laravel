<?php
namespace App\Services\json;

class JpLimitedCard extends AbstractCard
{
    public function __construct($json)
    {
        parent::__construct($json);
        $this->jp = $this->getJp($json);
    }

    public function jpname(string $enname):string
    {
        // STA対応
        if (empty($this->jp)) {
            return $this->getJpnameByAPI($enname);
        }
        return parent::jpname($enname);
    }


    public function multiverseId()
    {
        return '';
    }

    public function scryfallId()
    {
        return $this->getEnScryfallId();
    }

    public function number()
    {
        $number = parent::number();
        return str_replace('★', '', $number);
    }

    public function promotype() {
        // STA日本画対応
        if (strcmp($this->setcode(), 'STA') == 0) {
            return 'jpainting';
        }
        return parent::promotype();
    }

}
?>