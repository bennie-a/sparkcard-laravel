<?php
namespace App\Services\json;

class JpCard extends AbstractCard
{
    public function __construct($json)
    {
        parent::__construct($json);
        $this->jp = $this->getJp($json);
    }

    public function multiverseId()
    {
        return $this->jp["multiverseId"];
    }
}
?>