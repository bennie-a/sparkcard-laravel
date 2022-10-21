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

    public function test入力値がartifact() {
        $color = CardColor::matchByString("artifact");
        assertEquals(CardColor::ARTIFACT, $color);
        $param = $color->color();
        Log::debug($param);
        assertCount(0, $param, "パラメータ(色)の個数");
        assertCount(1, $color->cardtype(), "カードタイプの個数");
        assertEquals("artifact", $color->cardtype()[0], "カードタイプ");
    }

    public function test入力値がland() {
        $color = CardColor::matchByString("land");
        assertEquals(CardColor::LAND, $color);
        $param = $color->color();
        Log::debug($param);
        assertCount(0, $param, "パラメータ(色)の個数");
        assertCount(1, $color->cardtype(), "カードタイプの個数");
        assertEquals("land", $color->cardtype()[0], "カードタイプ");
        assertEquals("and", $color->cardtypeOpe(), "カードタイプの論理和");
        assertEquals("able", $color->colorMulti(), "多色を含んでもよい");
    }
    
}
