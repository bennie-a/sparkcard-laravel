<?php
namespace App\Services\json;

class JpLimitedCard extends AbstractCard
{
    public function __construct($json)
    {
        parent::__construct($json);
        $this->jp = $this->getJp($json);
    }

    public function multiverseId()
    {
        return '';
    }

    public function scryfallId()
    {
        return $this->json['identifiers']['scryfallId'];
    }

    public function number()
    {
        $number = parent::number();
        return str_replace('★', '', $number);
    }

}
?>