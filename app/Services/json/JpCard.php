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
    {   $id = 'multiverseId';
        return MtgJsonUtil::hasKey($id, $this->getJpData()) ? $this->getJpData()[$id] : parent::getEnMultiverseId();
    }

    public function getJpData() {
        return $this->jp;
    }
}
?>