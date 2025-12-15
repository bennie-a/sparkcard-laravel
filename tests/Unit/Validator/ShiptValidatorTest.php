<?php

namespace Tests\Unit\Validator;

use App\Http\Validator\ShiptValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;
use Tests\Unit\DB\Shipt\ShiptLogTestHelper;

/**
 * ShiptValidatorのテストクラス
 */
#[CoversClass(ShiptValidator::class)]
class ShiptValidatorTest extends TestCase
{
    #[TestDox('「/api/shipping/parse」からAPIを実行してエラー情報が取得できるか確認する。')]
    public function testErrorFromApi(): void
    {
        ShiptLogTestHelper::createTodayOrderInfos();
        $response = $this->postJson('/api/shipping/parse');

        $response->assertStatus(200);
    }
}
