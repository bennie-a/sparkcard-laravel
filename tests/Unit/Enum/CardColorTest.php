<?php

namespace Tests\Unit;

use App\Enum\CardColor;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

class CardColorTest extends TestCase
{
    /**
     *
     */
    public function test入力値がred()
    {
        $color = CardColor::matchByString("red");
        assertEquals(CardColor::RED, $color);
        $param = $color->color();
        Log::debug($param);
        assertCount(1, $param, "パラメータ(色)の個数");
        assertEquals("red", $param[0]);
    }

    public function test入力値がmulti() {
        $color = CardColor::matchByString("multi");
        assertEquals(CardColor::MULTI, $color);
        $param = $color->color();
        Log::debug($param);
        assertCount(1, $param, "パラメータ(色)の個数");
        assertEquals("non-colorless", $param[0]);
    }
}
