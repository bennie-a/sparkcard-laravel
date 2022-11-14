<?php
namespace App\Services\json;

class JpLimitedCard extends AbstractCard
{
    public function __construct($json)
    {
        parent::__construct($json);
        $this->jp = $this->getJp($json);
    }

    public function build(string $enname, $json)
    {
        
    }
}
?>