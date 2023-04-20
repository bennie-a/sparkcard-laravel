<?php
namespace App\Services\json;

use App\Libs\MtgJsonUtil;

class JpCard extends AbstractCard
{
    public function __construct($json)
    {
        parent::__construct($json);
        $this->jp = $this->getJp($json);
    }

    public function multiverseId()
    {
        return MtgJsonUtil::hasKey(self::MULTIVERSEID, $this->getJpData()) ?
                 $this->getJpData()[self::MULTIVERSEID] : parent::getEnMultiverseId();
    }

    public function getJpData() {
        return $this->jp;
    }
}
?>