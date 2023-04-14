<?php

namespace Tests\Unit\DB;

use App\Models\Expansion;
use App\Repositories\Api\Notion\ExpansionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function Ramsey\Uuid\Lazy\toString;

class DBExpTest extends TestCase
{
    public function setup():void
    {
        parent::setUp();
        DB::table('expansion')->truncate();
    }

    public function test_全項目入力()
    {
        $storeData = [
         'name'=>'コールドスナップ',
         'attr' => 'CSP',
        'base_id' => 4517385,
        'release_date' => '2006-07-21'];
        $this->execute($storeData);
        // $this->post('api/database/exp', $storeData)->assertStatus(Response::HTTP_CREATED);

        // DBからデータ取得
        $dbactual = Expansion::where('attr', $storeData['attr'])->first();
        assertNotNull($dbactual, 'DB登録の有無');
        assertEquals($dbactual->name, $storeData['name'], '名称');
        assertEquals($dbactual->attr, $storeData['attr'], '略称');
        assertEquals($dbactual->base_id, $storeData['base_id'], 'BASE_ID');
        assertEquals($dbactual->release_date, $storeData['release_date'], 'リリース日');
    }

    public function test_BASEIDが0()
    {
        $storeData = [
         'name'=>'コールドスナップ',
         'attr' => 'CSP',
        'base_id' => 0,
        'release_date' => '2006-07-21'];
        $this->execute($storeData);
        // $this->post('api/database/exp', $storeData)->assertStatus(Response::HTTP_CREATED);
    }

    // /**
    //  * test
    //  */
    // public function test_BASEIDが未入力()
    // {
    //     $storeData = ['id' => '008b0ade-0521-462a-b1b3-93c7e8c8406c',
    //      'name'=>'コールドスナップ',
    //      'attr' => 'CSP',
    //     'release_date' => '2006-7-21'];
    //     $this->post('api/database/exp', $storeData)->assertStatus(Response::HTTP_CREATED);
    // }

    private function execute($storeData) {
        $this->post('api/database/exp', $storeData)->assertStatus(Response::HTTP_CREATED);

        // DBからデータ取得
        $dbactual = Expansion::where('attr', $storeData['attr'])->first();
        assertNotNull($dbactual, 'DB登録の有無');
        assertEquals($dbactual->name, $storeData['name'], '名称');
        assertEquals($dbactual->attr, $storeData['attr'], '略称');
        assertEquals($dbactual->base_id, $storeData['base_id'], 'BASE_ID');
        assertEquals($dbactual->release_date, $storeData['release_date'], 'リリース日');

        $repo = new ExpansionRepository();
        $actualPage = $repo->findById($dbactual->notion_id);
        assertNotNull($actualPage, 'Notion登録の有無');
        $array = $actualPage->toArray();
        assertEquals($storeData['name'], $array['title'] , '名称');
        $attr = $actualPage->getProperty('略称')->getRawContent()[0]['plain_text'];
        assertEquals($storeData['attr'], $attr,  '略称');
        // assertEquals($dbactual->base_id, $storeData['base_id'], 'BASE_ID');
        // assertEquals($dbactual->release_date, $storeData['release_date'], 'リリース日');
    }
}
