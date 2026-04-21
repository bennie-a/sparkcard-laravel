<?php

namespace Tests\Feature\tests\Unit\Auth;

use App\Models\BaseToken;
use App\Repositories\Api\Baseshop\BaseApiRepository;
use App\Repositories\Api\Baseshop\BaseApiRepositoryInterface;
use Carbon\CarbonImmutable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

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
    public function test_callback(): void
    {
        $mock = Mockery::mock(BaseApiRepository::class)->makePartial();
        $code = 'test_code';
        $exToken = [
                'access_token' => Uuid::uuid4()->toString(),
                'token_type' => 'bearer',
                'refresh_token' => Uuid::uuid4()->toString(),
                'expires_in' => 3600];

        $mock->shouldReceive('getAccessToken')
            ->once()
            ->with($code)
            ->andReturn($exToken);

        $this->app->instance(BaseApiRepositoryInterface::class, $mock);
        $params = ['code' => $code];
        $response = $this->post('/api/base/oauth/callback', $params);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonPath('connected', true);

        $this->assertEquals(1, BaseToken::all()->count());
        $token = BaseToken::first();
        $this->assertEquals($exToken['access_token'], $token->access_token);
        $this->assertEquals($exToken['refresh_token'], $token->refresh_token);

        $diff  = $token->expires_at->diffInSeconds(CarbonImmutable::now()->addHour());
        $this->assertLessThanOrEqual(5, $diff); // 5秒以内の誤差を許容
        }
}
