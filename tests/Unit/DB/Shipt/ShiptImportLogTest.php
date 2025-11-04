<?php

namespace Tests\Feature\tests\Unit\DB\Shipt;

use App\Files\Csv\CsvWriter;
use App\Files\Reader\ShiptLogCsvReader;
use App\Files\Stock\ShippingLogCsvReader;
use App\Models\Stockpile;
use App\Services\Constant\StockpileHeader as Header;
use Illuminate\Http\Response;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\TestCase;

/**
 * 出荷ログ登録に関するテスト
 */
class ShiptImportLogTest extends TestCase
{
    public function setup():void {
        parent::setup();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestCardInfoSeeder::class);
        $this->seed(TestStockpileSeeder::class);
     }
    /**
     * A basic feature test example.
     *@dataProvider okprovider
     * @return void
     */
    #[DataProvider('okprovider')]
    public function test_ok(String $filename)
    {      
        $json = ['total_rows' => 2, 'successful_rows' => 2,
                        'failed_rows' => 0, 'failed_details' => [],
                        'skip_rows' => 0, "skip_details" => []];
        $this->execute($filename, Response::HTTP_CREATED, $json);
    }
    
    public static function okprovider() {
        return [
            '全件成功' => ['all_success.csv'],
            'ファイルの文字コードがShift-JIS' => ['shift-jis.csv'],
            'ファイルの文字コードがUTF-8' => ['utf-8.csv']
        ];
    }

    /**
     * 一部成功パターン
     *
     * @return void
     */
    public function test_partical() {
        $json = ['total_rows' => 2, 'successful_rows' => 1,
                         'failed_rows' => 1, 'failed_details' => [["number" => "3", "reason" => "該当するNotionカードがありません"]],
                          'skip_rows' => 0, "skip_details" => []];
        $this->execute("partical_success.csv", Response::HTTP_MULTI_STATUS, $json);
    }

    /**
     * OKパターン、一部OKパターンのテストを実行する。
     *
     * @param string $filename
     * @param integer $status
     * @param array $json
     * @return void
     */
    private function execute(string $filename, int $status, array $json) {
        $dir = dirname(__FILE__, 5).'\storage\test\sms\shipt\\';
        $response = $this->post('/api/shipping/import', ['path' => $dir.$filename]);
        logger()->debug($response->json());
        
        $response->assertStatus($status);
        $response->assertJson($json);
    }

    /**
     * 登録をスキップするケース
     *@dataProvider skipprovider
     * @return void
     */
    #[DataProvider('skipprovider')]
    public function test_skipcase(string $filename, array $json) 
    {
        $this->execute($filename, 201, $json);
    }

    public static function skipprovider() {
        return [
            '重複した出荷登録(在庫あり)' => ["duplicate.csv",
            ['total_rows' => 2, 'successful_rows' => 1,
            'failed_rows' => 0, 'failed_details' => [],
            'skip_rows' => 1, "skip_details" => [["number" => "3", "reason" => "既に登録されています"]]]],
            '重複した出荷登録(在庫が0枚)' => ["duplicate_stock0.csv",  
            ['total_rows' => 2, 'successful_rows' => 1,
            'failed_rows' => 0, 'failed_details' => [],
            'skip_rows' => 1, "skip_details" => [["number" => "3", "reason" => "既に登録されています"]]]],

        ];
    }

    /**
     * CSVファイルのフォーマットに関するエラー
     *@dataProvider fileprovider
     * @return void
     */
    #[DataProvider('fileprovider')]
    public function test_fileerror($filename, int $statusCode, string $status, string $error) {
        $json = ["status" => $status, "error" => $error];
        $this->execute($filename, $statusCode, $json);
    }

    public static function fileprovider() {
        return [
            'CSVヘッダー不足' => ['ng-noheader.csv', Response::HTTP_BAD_REQUEST,
                                                        'CSV Validation Error', 'CSVファイルのヘッダーが足りません'],
            '指定したファイルが存在しない' => ['xxx.csv', Response::HTTP_BAD_REQUEST,
                                                                            'File Not Found', 'ファイルが存在しません']

        ];
    }

    /**
     * NGケース
     *
     * @param string $filename
     * @param integer $status
     * @param array $json
     * @dataProvider ngprovider
     */
    #[DataProvider('ngprovider')]
    public function test_ng(string $item_name, string $msg) {
        $reader = new ShiptLogCsvReader();
        $dir = config('csv.export');
        $row = $reader->read("{$dir}shipping_log.csv");
        $newRow = [];
        $r = current($row);
        foreach (Header::shippinglog_constants() as $h) {
            if ($h === Header::PRODUCT_NAME) {
                $newRow[0][$h] = $item_name;
                continue;
            }
            if ($item_name === '【XLN】軍団の上陸[JP][白]' && $h === Header::QUANTITY) {
                $stock = Stockpile::find('軍団の上陸', 'XLN', 'NM', 'JP', false);
                $this->assertNotNull($stock, '在庫情報が存在しません');
                $newRow[0][$h] = $stock->quantity + 1; // 在庫が0枚以下
                continue;
            }
            $newRow[0][$h] = $r[$h];
        }

        $writer = new CsvWriter();
        $newfile = 'tmp.csv';
        $writer->write($newfile, Header::shippinglog_constants(), $newRow);
        $json =  ['total_rows' => 1, 'successful_rows' => 0,
            'failed_rows' => 1, 'failed_details' => [["number" => "2", "reason" => $msg]],
            'skip_rows' => 0, "skip_details" => []];
        $this->execute($newfile, 445, $json);
    }

    public static function ngprovider() {
        return [
            '在庫が0枚' => ['【BRO】ドラゴンの運命[JP][赤]',  "在庫が0枚です。"],
            '在庫情報が存在しない' => ['【XLN】在庫情報なし[JP][白]', "在庫データがありません"],
            '出荷後の在庫が0枚未満' => ['【XLN】軍団の上陸[JP][白]', "在庫が足りません。"],
        ];
    }
    
    protected function tearDown():void {
        parent::tearDown();
        $tmpcsv  ="{config('csv.export')}tmp.csv";
        $result = false;
        if (!file_exists($tmpcsv)) {
            return;
        }
        if (is_writable($tmpcsv)) {
            $result = unlink($tmpcsv);
            if ($result) {
                logger()->info('tmp.csvを削除しました');
            } else {
                logger()->info('tmp.csvを削除できませんでした');
            }
        }
        Mockery::close();
    }
}
