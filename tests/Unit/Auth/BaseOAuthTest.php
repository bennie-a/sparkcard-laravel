<?php

namespace Tests\Feature\tests\Unit\Auth;

use App\Exceptions\api\Baseshop\BaseApiException;
use App\Models\BaseToken;
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

        $this->verifyToken($exToken);
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
        $this->saveBaseToken($extoken);

        $this->executeStatusTest(true);
    }

    #[Test]
    #[Group('status')]
    #[TestDox('status:アクセストークンが有効期限切れなら再発行した上で「連携済み」')]
    public function ok_status_expired() {
        $expiredToken = $this->createRandomToken();
        $this->saveBaseToken($expiredToken, -3660); // 有効期限を1時間1分前に設定

        $exToken = $this->createRandomToken();
        $this->mockRepository('refreshAccessToken', $expiredToken[BCon::REFRESH_TOKEN], $exToken);

        $this->executeStatusTest(true);
        $this->verifyToken($exToken);
    }

    #[Test]
    #[Group('status')]
    #[TestDox('status:アクセストークンが未登録なら「未連携」')]
    public function ng_status_no_token() {
        $this->executeStatusTest(false);
    }

    #[Test]
    #[Group('status')]
    #[TestDox('status:BASE APIのトークン再発行に失敗した場合、HTTPステータスコード「400」とエラーメッセージを返す。')]
    public function ng_status_expired_refresh_token() {
        $exError = [
            'error' => 'invalid_request',
            'error_description' => 'リフレッシュトークンの有効期限が切れています。'
        ];

        $extoken = $this->createRandomToken();
        $this->saveBaseToken($extoken, -5000);

        $mock = Mockery::mock(BaseOAuthRepository::class)->makePartial();
        $mock->shouldReceive('refreshAccessToken')
            ->once()
            ->with($extoken[BCon::REFRESH_TOKEN])
            ->andThrow(new BaseApiException(json_encode($exError)));

        $this->app->instance(BaseOAuthRepository::class, $mock);
        $response = $this->get('/api/base/oauth/status');
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJsonPath('detail', $exError['error_description']);
    }

    /**
     * エンドポイントが'/api/base/oauth/status'のレスポンスを
     * 検証する。
     *
     * @param boolean $isConnected
     * @return void
     */
    private function executeStatusTest(bool $isConnected) {
        $response = $this->get('/api/base/oauth/status');
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonPath(BCon::CONNECTED, $isConnected);

    }

    /**
     * base_tokenテーブルにレコードを1件登録する。
     *
     * @param array $extoken ランダムなアクセストークン情報
     * @param integer $expiresIn アクセストークンの有効期限（秒）
     * @return void
     */
    private function saveBaseToken(array $extoken, int $expiresIn = 3600) {
        BaseToken::create([
            BCon::ACCESS_TOKEN => $extoken[BCon::ACCESS_TOKEN],
            BCon::REFRESH_TOKEN => $extoken[BCon::REFRESH_TOKEN],
            BCon::EXPIRES_IN => now()->addSeconds($expiresIn)
        ]);
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
     * base_tokenテーブルのレコードを検証する。
     *
     * @param array $exToken
     * @return void
     */
    private function verifyToken(array $exToken) {
        $this->assertEquals(1, BaseToken::all()->count());
        $token = BaseToken::first();
        $this->assertEquals($exToken[BCon::ACCESS_TOKEN], $token->access_token);
        $this->assertEquals($exToken[BCon::REFRESH_TOKEN], $token->refresh_token);

        $diff  = $token->expires_in->diffInSeconds(CarbonImmutable::now()->addHour());
        $this->assertLessThanOrEqual(5, $diff); // 5秒以内の誤差を許容
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
