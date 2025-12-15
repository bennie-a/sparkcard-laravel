<?php
namespace Tests\Trait;
use App\Http\Response\CustomResponse;
use App\Services\Constant\ErrorConstant as EC;
use App\Services\Constant\GlobalConstant as GC;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;

/**
 * エラー情報を検証するTrait
 */
trait ApiErrorAssertions {

    /**
     * CSVファイルの内容にエラーが発生した時のエラー情報を検証する。
     *
     * @param TestResponse $response
     * @param string $endpoint
     * @param array $rowError
     * @return void
     */
    public function assertCsvRowError(TestResponse $response, string $endpoint, array $rowError) {
        $response->assertJson(function(AssertableJson $json) use($endpoint, $rowError) {
            $json->hasAll([EC::TITLE, GC::STATUS, EC::REQUEST, EC::DETAIL, EC::ROWS]);
            $json->whereAll([
                EC::TITLE => '不正な記載',
                EC::DETAIL => 'CSVファイルに不正な記載があります。',
                GC::STATUS => CustomResponse::HTTP_CSV_VALIDATION,
                EC::REQUEST => $endpoint,
                EC::ROWS.".0.row" => $rowError[EC::ROW],
                EC::ROWS.".0.msg" => $rowError[EC::MSG],
            ]);
        });
    }

}
