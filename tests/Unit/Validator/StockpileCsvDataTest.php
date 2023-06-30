<?php

namespace Tests\Unit\Validator;

use App\Files\Stock\StockpileCsvReader;
use App\Http\Response\CustomResponse;
use App\Services\Stock\StockpileService;
use App\Http\Validator\StockpileValidator;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNotNull;

/**
 * 在庫管理CSVの入力値テスト
 */
class StockpileCsvDataTest extends TestCase
{
    /**
     * ｒA basic feature test example.
     *
     * @return void
     * @dataProvider dataprovider
     */
    public function test_ok(string $file, int $number, string $exError)
    {
        $dir = dirname(__FILE__, 4).'\storage\test\sms\\';
        $reader = new StockpileCsvReader();
        $rows = $reader->read($dir.$file);
        $validator = new StockpileValidator();

        $errors = $validator->validate($rows);
        assertNotEmpty($errors);
        $actual = current($errors);
        $msg = $actual[$number];
        assertNotNull($msg);
        assertEquals($exError, current($msg));
        logger()->debug($actual);
    }

    public function dataprovider() {
        return [
            '商品名' => ['item_name.csv',  3, '商品名は必ず入力してください。'],
            '言語' => ['lang.csv',  7, '入力した言語は不正です。'],
            '保存状態' => ['condition.csv',  7, '入力した保存状態は不正です。'],
            '数量' => ['quantity.csv',  3, '数量は半角数字を入力してください。'],
        ];
    }
}

