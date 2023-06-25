<?php
namespace app\Services\json;

use App\Libs\MtgJsonUtil;
use App\Services\json\AbstractCard;

class ScryfallCard extends AbstractCard {

    /**
     *@inheritDoc AbstractCard multiverseId
     */
    public function multiverseId(){
        return current($this->getJson()['multiverse_ids']);
    }

    /**
     * カード名(日)を取得する。
     *
     * @return string
     */
    public function name():string {
        return $this->getJson()['printed_name'];
    }

    /**
     * カード名(英)を取得する。
     *
     * @return string
     */
    public function enname():string {
        return $this->getJson()[self::NAME];
    }

    /**
     * 色の種類を取得する。
     *
     * @return array
     */
    public function colors():array {
        return $this->getJson()['color_identity'];
    }

    public function cardtype():string {
        return $this->getJson()['type_line'];
    }

    protected function isFullArt() {
        return \boolval($this->getJson()[('full_art')]);
    }

    public function imageurl() {
        return $this->getJson()['image_uris']['png'];
    }

    protected function hasPromotype() {
        return MtgJsonUtil::hasKey('promo_types', $this->json);
    }

    protected function promotypeKey() : string {
        return 'promo_types';
    }

    /**
     * @inheritDoc AbstractCard frameEffects
     *
     * @return string
     */
    public function frameEffects() {
        $border = $this->getJson()['border_color'];
        if ($this->getJson()['border_color'] !== 'black') {
            return $border;
        }
        return parent::frameEffects();
    }

}