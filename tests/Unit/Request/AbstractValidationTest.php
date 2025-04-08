<?php

namespace Tests\Unit\Request;

use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator as ValidationValidator;

/**
 * バリデーションテストの共通クラス
 */
abstract class AbstractValidationTest extends TestCase
{

    protected function ok_pattern(array $data):void
    {
            $validator = $this->validate($data);
            $isPassed = $validator->passes();
            if ($validator->fails()) {
                logger()->error($validator->errors());
            }
            $this->assertTrue($isPassed, 'バリデーション失敗');        
    }

    /**
     * NGパターン
     *
     * @param array $data
     * @dataProvider ngdata
     */
    protected function ng_pattern(array $data, array $msgs) {
        $validator = $this->validate($data);
        $isPassed = $validator->passes();
        $this->assertFalse($isPassed);

        foreach($msgs as $key => $m) {
            $actual = $validator->messages()->get($key);
            logger()->debug($actual);
            $this->assertNotEmpty($actual, 'メッセージの有無');
            $this->assertEquals($m, current($actual), 'メッセージの合致');
        };
    }

    /**
     * バリデーションチェックを実行する。
     *
     * @param array $data
     * @return ValidationValidator
     */
    protected function validate(array $data) {
        
        $request = $this->createRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules, $request->messages(), $request->attributes());
        // $request->withValidator($validator);
        return $validator;
    }

    abstract protected function createRequest():FormRequest;
}