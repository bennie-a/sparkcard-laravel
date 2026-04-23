<?php

namespace Tests\Feature\tests\Unit\Auth;

use App\Exceptions\api\Baseshop\BaseApiException;
use App\Models\BaseToken;
use App\Repositories\Api\Baseshop\BaseApiRepository;
use App\Repositories\Api\Baseshop\BaseOAuthRepository;
use Carbon\CarbonImmutable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;
use App\Services\Constant\BaseApiConstant as BCon;

#[TestDox('BASE APIの認証関連のテスト')]
#[CoversClass(BaseOAuthController::class)]
class BaseOAuthTest extends TestCase
{
    public function setup():void
    {
        parent::setUp();
        DB::table('base_token')->truncate();
    }

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
    public function ok_callback(): void
    {
        $code = 'test_code';
        $exToken = [
            BCon::ACCESS_TOKEN => Uuid::uuid4()->toString(),
            'token_type' => 'bearer',
            BCon::REFRESH_TOKEN => Uuid::uuid4()->toString(),
            BCon::EXPIRES_IN => 3600];

        $this->mockRepository('getAccessToken', $code, $exToken);
        $params = ['code' => $code];
        $response = $this->post('/api/base/oauth/callback', $params);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonPath(BCon::CONNECTED, true);

        $this->assertEquals(1, BaseToken::all()->count());
        $token = BaseToken::first();
        $this->assertEquals($exToken[BCon::ACCESS_TOKEN], $token->access_token);
        $this->assertEquals($exToken[BCon::REFRESH_TOKEN], $token->refresh_token);

        $diff  = $token->expires_in->diffInSeconds(CarbonImmutable::now()->addHour());
        $this->assertLessThanOrEqual(5, $diff); // 5秒以内の誤差を許容
    }

    #[Test]
    #[TestDox('認可コードが不正な場合のテスト')]
    public function ng_invalid_code() {
        $code = 'test_code';
        $exToken = [
            'error' => 'invalid_request',
            'error_description' => '不正な認可コードです。.'
        ];

        $params = ['code' => $code];
        $mock = Mockery::mock(BaseOAuthRepository::class)->makePartial();
        $mock->shouldReceive('getAccessToken')
            ->once()
            ->with($code)
            ->andThrow(new BaseApiException(json_encode($exToken)));

        $this->app->instance(BaseOAuthRepository::class, $mock);
        $response = $this->post('/api/base/oauth/callback', $params);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    #[Test]
    #[TestDox('BASE APIの連携ステータスがOKか確認する。')]
    public function ok_status() {
        $response = $this->get('/api/base/oauth/status');
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonPath(BCon::CONNECTED, true);
    }

    private function mockRepository($method, $code, $exToken)
    {
        $mock = Mockery::mock(BaseOAuthRepository::class)->makePartial();
        $mock->shouldReceive($method)
            ->once()
            ->with($code)
            ->andReturn($exToken);

        $this->app->instance(BaseOAuthRepository::class, $mock);
    }
}
