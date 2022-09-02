<?php
namespace App\Repositories\Api\Notion;
use App\Repositories\Api\Notion\NotionRepository;
use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Query\Filters\Filter;
use FiveamCode\LaravelNotionApi\Query\Filters\Operators;
use Illuminate\Support\Collection;

//Notionのエキスパンションテーブルへの接続
class ExpansionRepository extends NotionRepository{

    public function __construct()
    {
        $databaseId = config('notion.expansion');
        parent::__construct($databaseId);
    }

    //　名前からエキスパンション名を取得する。
    public function findIdByName(string $name){
        $notion = parent::createNotion();
        $filters = new Collection();
        $filters->add(Filter::textFilter("名前", Operators::EQUALS, $name));
        $pages = $notion->database($this->databaseId)->filterBy($filters)->query()->asCollection();
        $p = $pages[0];
        return str_replace('-', '', $p->getId());
    }
}
?>