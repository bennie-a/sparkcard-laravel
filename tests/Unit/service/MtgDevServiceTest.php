<?php

namespace Tests\Unit\service;

use App\Services\MtgDevService;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;

class MtgDevServiceTest extends TestCase
{
    public function setUp(): void {
        $this->service = new MtgDevService();
    }

    /**
     * 
     */
    public function カード情報取得_単色() {
        $name = "Liliana of the Veil";
        $card = $this->service->getCardInfo($name, "DMU");
        assertNotNull($card, "カード情報の有無");
        assertEquals("575882", $card["id"], "ID");  
        assertEquals("黒", $card["color"], "色");
        assertEquals("http://gatherer.wizards.com/Handlers/Image.ashx?multiverseid=575882&type=card", $card["image"], "画像URL");
    }

    /**
     * 
     */
    public function カード情報取得_多色() {
        $card = $this->service->getCardInfo("Balmor, Battlemage Captain", "DMU");
        assertNotNull($card, "カード情報の有無");
        assertEquals("575981", $card["id"],  "ID");
        assertEquals("多色", $card["color"],  "色");
        assertEquals("http://gatherer.wizards.com/Handlers/Image.ashx?multiverseid=575981&type=card", $card["image"], "画像URL");
    }

    
    /**
     * 
     */
    public function カード情報取得_金枠() {
        $card = $this->service->getCardInfo("Jodah, the Unifier", "DMU");
        assertNotNull($card, "カード情報の有無");
        assertEquals("575988", $card["id"],  "ID");
        assertEquals("多色", $card["color"],  "色");
        assertEquals("http://gatherer.wizards.com/Handlers/Image.ashx?multiverseid=575988&type=card", $card["image"], "画像URL");
    }

    /**
     * 
     */
    public function カード情報取得_無色() {
        $card = $this->service->getCardInfo("Karn, Living Legacy", "DMU");
        assertNotNull($card, "カード情報の有無");
        assertEquals("575786", $card["id"],  "ID");
        assertEquals("無色", $card["color"],  "色");
        assertEquals("http://gatherer.wizards.com/Handlers/Image.ashx?multiverseid=575786&type=card", $card["image"], "画像URL");
    }

    /**
     * @test
     */
    public function カード情報取得_アーティファクト() {
        $card = $this->service->getCardInfo("Karn's Sylex", "DMU");
        assertNotNull($card, "カード情報の有無");
        assertEquals("576019", $card["id"],  "ID");
        assertEquals("アーティファクト", $card["color"],  "色");
        assertEquals("http://gatherer.wizards.com/Handlers/Image.ashx?multiverseid=576019&type=card", $card["image"], "画像URL");
    }

    public function カード情報取得_該当なし() {
        $card = $this->service->getCardInfo("Tura Kennerud, Skyknight", "DMU");
        assertNotNull($card, "カード情報の有無");
    }
}
