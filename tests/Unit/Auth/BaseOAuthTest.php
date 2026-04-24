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
use PHPUnit\Framework\Attributes\Group;

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
        $exToken = $this->createRandomToken();

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
    #[Group('status')]
    #[TestDox('status:アクセストークンが有効期限内なら「連携済み」')]
    public function ok_status_exp() {
        $extoken = $this->createRandomToken();
        BaseToken::create([
            BCon::ACCESS_TOKEN => $extoken[BCon::ACCESS_TOKEN],
            BCon::REFRESH_TOKEN => $extoken[BCon::REFRESH_TOKEN],
            BCon::EXPIRES_IN => now()->addSeconds($extoken[BCon::EXPIRES_IN])
        ]);

        $response = $this->get('/api/base/oauth/status');
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonPath(BCon::CONNECTED, true);
    }

    #[Test]
    #[Group('status')]
    #[TestDox('status:アクセストークンが有効期限切れなら再発行した上で「連携済み」')]
    public function ok_status_expired() {

    }

    #[Test]
    #[Group('status')]
    #[TestDox('status:アクセストークンが未登録なら「未連携」')]
    public function ng_status_no_token() {

    }

    #[Test]
    #[Group('status')]
    #[TestDox('status:リフレッシュトークンが有効期限切れなら「未連携」')]
    public function ng_status_expired_refresh_token() {

    }

    #[Test]
    #[TestDox('再発行に失敗した場合、「未連携」と判断するテスト')]
    public function ng_status_faiure_refresh() {

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

    /**
     * テスト用のトークンをランダムに生成する。
     *
     * @return array
     */
    private function createRandomToken():array {
        $exToken = [
                BCon::ACCESS_TOKEN => Uuid::uuid4()->toString(),
                'token_type' => 'bearer',
                BCon::REFRESH_TOKEN => Uuid::uuid4()->toString(),
                BCon::EXPIRES_IN => 3600
        ];
        return $exToken;
    }
}
