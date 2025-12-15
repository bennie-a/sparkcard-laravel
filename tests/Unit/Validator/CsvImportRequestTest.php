<?php

namespace Tests\Unit\Validator;

use App\Http\Requests\CsvImportRequest;
use Illuminate\Support\Facades\Validator;

use Tests\TestCase;

/**
 * CsvImportRequestが存在しないため、動かない
 */
class CsvImportRequestTest extends TestCase
{
    /**
     * Undocumented function
     *
     * @param array $data
     * @param boolean $expected
     * @dataProvider dataprovider
     */
    public function test(array $data, bool $expected)
    {
        $request = new CsvImportRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules, $request->messages(), $request->attributes());
        $result = $validator->passes();
        $this->assertEquals($expected, $result);

        if (!$expected) {
            $actualmessage = $validator->messages()->get('path');
            logger()->debug($actualmessage);
            $expectedmessage = $data['msg'];
            $this->assertSame($expectedmessage, current($actualmessage), 'エラーメッセージ');
        }
    }

    public function dataprovider() {
        return [
            '正常' => [['path' => 'import.csv'], true],
            'ファイルパスなし' => [['path' => '', 'msg' => 'ファイルパスは必ず入力してください。'], false],
            '拡張子が.csvではない' => [['path' => 'import.xlsx', 'msg' => '入力したファイルパスはCSVファイルではありません。'], false],
        ];
    }
}
