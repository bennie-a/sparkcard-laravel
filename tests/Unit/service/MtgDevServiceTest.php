<?php

namespace Tests\Unit\service;

use App\Services\MtgDevService;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

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
        Log::debug($card);
        // $keys = array_keys($card);
    }
}
