<?php

namespace Tests\Feature\tests\Unit\Auth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

#[TestDox('BASE APIの認証関連のテスト')]
#[CoversClass(BaseOAuthController::class)]
class BaseOAuthTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    #[Test]
    #[TestDox('認可サーバーへのURLを取得するテスト')]
    public function connect(): void
    {
        $response = $this->get('/api/base/oauth/connect');
        $response->assertStatus(200);
        $expected = 'https://api.thebase.in/1/oauth/authorize?response_type=code&client_id=e344b1c2d02a1e9b2930cb81e9ca36b4&redirect_uri=https://sparkcard.vercel.app/&scope=read_items%20read_orders';

        $response->assertJsonPath('url', $expected);
    }

    #[Test]
    #[TestDox('アクセストークンとリフレッシュトークンを登録するテスト')]
    public function test_callback(): void
    {
        $code = 'test_code';
        $params = ['code' => $code];
        $response = $this->post('/api/base/oauth/callback', $params);
        $response->assertStatus(201);
        $response->assertJsonPath('code', $code);
    }
}
