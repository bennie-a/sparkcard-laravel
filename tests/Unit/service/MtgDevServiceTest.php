<?php

namespace Tests\Unit\service;

use App\Services\MtgDevService;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;

class MtgDevServiceTest extends TestCase
{
    /**
     * @test
     */
    public function カード情報を取得() {
        $service = new MtgDevService();
        $name = "ヴェールのリリアナ";
        $card = $service->getCardInfo($name, "DMU");
        assertNotNull($card, "カード情報の有無");
        assertEquals($card["id"], "575882", "ID");
        assertEquals($card["image"], "http://gatherer.wizards.com/Handlers/Image.ashx?multiverseid=575882&type=card", "画像URL");
    }
}
