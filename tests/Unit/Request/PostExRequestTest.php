<?php
namespace Tests\Unit\Request;

use App\Http\Requests\PostExRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

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
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();
        $this->assertEquals($expected, $result);
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
            ]
            ];
    }
}