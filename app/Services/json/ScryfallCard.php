<?php
namespace app\Services\json;

use App\Libs\MtgJsonUtil;
use App\Services\json\AbstractCard;
use App\Services\Constant\CardConstant as Con;


class ScryfallCard extends AbstractCard {

    /**
     *@inheritDoc AbstractCard multiverseId
     */
    public function multiverseId(){
        $ids = $this->getJson()['multiverse_ids'];
        if (empty($ids)) {
            return 0;
        }
        return current($ids);
    }

    /**
     * カード名(日)を取得する。
     *
     * @return string
     */
    public function name():string {
        if (!MtgJsonUtil::hasKey('printed_name', $this->getJson())) {
            return "";
        }
        return $this->getJson()['printed_name'];
    }

    /**
     * カード名(英)を取得する。
     *
     * @return string
     */
    public function enname():string {
        return $this->getJson()[Con::NAME];
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

    /**
     * @see App\Services\json\AbstractCard types
     *
     * @return array
     */
    #[\Override]
    public function types() {
        return [$this->cardtype()];
    }


    public function isFullArt() {
        return \boolval($this->getJson()[('full_art')]);
    }

    public function imageurl() {
        $imageuris = [];
        if ($this->getJson()['layout'] == 'transform') {
            $imageuris = $this->getJson()['card_faces'][0]['image_uris'];
        } else {
            $imageuris =  $this->getJson()['image_uris'];
        }


        return $imageuris['png'];
    }

    protected function hasPromotype() {
        return MtgJsonUtil::hasKey('promo_types', $this->getJson());
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

    public function number()
    {
        return $this->getJson()['collector_number'];
    }

    public function setcode():string {
        return \strtoupper($this->getJson()['set']);
    }

    public function reprint() {
        return $this->getJson()['reprint'];
    }

        /**
     * isTextlessの項目を取得する。
     * @see AbstractCard
     * @return boolean
     */
    public function isTextless() {
        return MtgJsonUtil::isTrue('textless', $this->getJson());
    }

    public function borderColor() {
        return $this->getJson()['border_color'];
    }
}