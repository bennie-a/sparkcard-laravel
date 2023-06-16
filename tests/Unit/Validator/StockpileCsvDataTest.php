<?php

namespace Tests\Unit\Validator;

use App\Http\Response\CustomResponse;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;

class StockpileCsvDataTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     * @dataProvider dataprovider
     */
    public function test_ok(string $file, int $number, string $exError)
    {
        $dir = dirname(__FILE__, 4).'\storage\test\sms\\';
        $response = $this->post('api/stockpile/import', ['path' => $dir.$file]);
        $response->assertStatus(CustomResponse::HTTP_CSV_VALIDATION);
        $errors = $response->json('errors');
        $actual = current($errors);
        $msg = $actual[$number];
        assertNotNull($msg);
        assertEquals($exError, current($msg));
        logger()->debug($actual);
    }

    public function dataprovider() {
        return [
            'セット略称' => ['setcode.csv',  3, 'セット略称は必ず入力してください。'],
            '商品名' => ['item_name.csv',  3, '商品名は必ず入力してください。'],
            '言語' => ['lang.csv',  7, '入力した言語は不正です。'],
            '保存状態' => ['condition.csv',  7, '入力した保存状態は不正です。'],
            '数量' => ['quantity.csv',  3, '数量は半角数字を入力してください。'],
        ];
    }
}

