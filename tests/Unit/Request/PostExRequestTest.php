<?php
namespace Tests\Unit\Request;

use App\Http\Requests\PostExRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

use function Psy\debug;

class PostExRequestTest extends TestCase {

    /**
     * Undocumented function
     *
     * @param array $data
     * @param boolean $expected
     * @dataProvider dataprovider
     */
    public function testPostRequest(array $data, bool $expected) {
        $request = new PostExRequest();
        $rules = $request->rules();
        $validator = Validator::make($data, $rules, $request->messages(), $request->attributes());
        $result = $validator->passes();
        $this->assertEquals($expected, $result);

        if (!$expected) {
            $actualmessage = $validator->messages()->get($data['error_key']);
            logger()->debug($actualmessage);
            $expectedmessage = $data['msg'];
            $this->assertSame($expectedmessage, current($actualmessage), 'エラーメッセージ');
        }
    }

    public function dataprovider() {
        return [
            '正常' => [
                    [
                        'name' => 'コールドスナップ',
                        'attr'=>'CSP',
                        'block'=>'アイスエイジ',
                        'format'=>'モダン',
                        'release_date'=>'2006-07-21'
                    ],
                    true
                ],
            '名称なし' => [
                [
                    'name' => '',
                    'attr'=>'CSP',
                    'block'=>'アイスエイジ',
                    'format'=>'モダン',
                    'release_date'=>'2006-07-21',
                    'error_key' => 'name',
                    'msg' => '名称を入力してください。'
                ],
                false
            ],
            '略称なし' => [
                [
                    'name' => 'コールドスナップ',
                    'attr'=>'',
                    'block'=>'アイスエイジ',
                    'format'=>'モダン',
                    'release_date'=>'2006-07-21',
                    'error_key' => 'attr',
                    'msg' => '略称を入力してください。'
                ],
                false
            ],
            'ブロックなし' => [
                [
                    'name' => 'コールドスナップ',
                    'attr'=>'CSP',
                    'block'=>'',
                    'format'=>'モダン',
                    'release_date'=>'2006-07-21',
                    'error_key' => 'block',
                    'msg' => 'ブロックを入力してください。'

                ],
                false
            ],
            'フォーマットなし' => [
                [
                    'name' => 'コールドスナップ',
                    'attr'=>'CSP',
                    'block'=>'アイスエイジ',
                    'format'=>'',
                    'release_date'=>'2006-07-21',
                    'error_key' => 'format',
                    'msg' => 'フォーマットはスタンダード、パイオニア、モダン、レガシー、統率者、マスターピース、その他のどれかを入力してください。'
                ],
                false
            ],
            'リリース日なし' => [
                [
                    'name' => 'コールドスナップ',
                    'attr'=>'CSP',
                    'block'=>'アイスエイジ',
                    'format'=>'モダン',
                    'release_date'=>''
                ],
                true
            ],
            'フォーマット_スタンダード' => [
                [
                    'name' => '機械兵団の進軍',
                    'attr'=>'MOM',
                    'block'=>'ファイレクシア',
                    'format'=>'スタンダード',
                    'release_date'=>'2023-04-21'
                ],
                true
            ],
            'フォーマット_パイオニア' => [
                [
                    'name' => 'カラデシュ',
                    'attr'=>'KLD',
                    'block'=>'カラデシュ',
                    'format'=>'パイオニア',
                    'release_date'=>'2006-07-21'
                ],
                true
            ],
            'フォーマット_モダン' => [
                [
                    'name' => 'モダンホライゾン',
                    'attr'=>'MH1',
                    'block'=>'その他',
                    'format'=>'モダン',
                    'release_date'=>'2006-07-21'
                ],
                true
            ],
            'フォーマット_レガシー' => [
                [
                    'name' => '第3版',
                    'attr'=>'3ED',
                    'block'=>'基本セット',
                    'format'=>'レガシー',
                    'release_date'=>'2006-07-21'
                ],
                true
            ],
            'フォーマット_統率者' => [
                [
                    'name' => '機械兵団の進軍_統率者',
                    'attr'=>'MOM',
                    'block'=>'ファイレクシア',
                    'format'=>'統率者',
                    'release_date'=>'2023-04-21'
                ],
                true
            ],
            'フォーマット_マスターピース' => [
                [
                    'name' => '機械兵団の進軍',
                    'attr'=>'MOM',
                    'block'=>'ファイレクシア',
                    'format'=>'マスターピース',
                    'release_date'=>'2006-07-21'
                ],
                true
            ],
            'フォーマット_その他' => [
                [
                    'name' => '機械兵団の進軍',
                    'attr'=>'MOM',
                    'block'=>'ファイレクシア',
                    'format'=>'その他',
                    'release_date'=>'2006-07-21'
                ],
                true
            ],
            'フォーマット_選択肢以外' => [
                [
                    'name' => '機械兵団の進軍',
                    'attr'=>'MOM',
                    'block'=>'ファイレクシア',
                    'format'=>'EDH',
                    'release_date'=>'2006-07-21',
                    'error_key' => 'format',
                    'msg' => 'フォーマットはスタンダード、パイオニア、モダン、レガシー、統率者、マスターピース、その他のどれかを入力してください。'
                ],
                false
            ],
        ];
    }
}