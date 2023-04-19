<?php
namespace App\Repositories\Api\Notion;
use App\Repositories\Api\Notion\NotionRepository;
use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Entities\Properties\Property;
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

    /**
     * 全エキスパンションを取得する。
     */
    public function findAll()
    {
       $notion = parent::createNotion();
       $pages = $notion->database($this->databaseId)->query()->asCollection();
       $contents = $pages[0];
       return $pages;
    }

    /**
     * 名前からエキスパンションIDを取得する。
     *
     * @param string $name
     * @return string エキスパンションID
     */
    public function findIdByName(string $name){
        $notion = parent::createNotion();
        $filters = new Collection();
        $filters->add(Filter::textFilter("名前", Operators::EQUALS, $name));
        $pages = $notion->database($this->databaseId)->filterBy($filters)->query()->asCollection();
        $p = $pages[0];
        return str_replace('-', '', $p->getId());
    }

    /**
     * 略称に該当するエキスパンションのIDを取得する。
     *
     * @param string $attr 略称
     * @return string 
     */
    public function findByAttr(string $attr) {
        $page = $this->findByEquals("略称", $attr);
        return str_replace('-', '', $page->getId());
    }

    public function findByPage(Property $prop) {
        $id = $prop->getContent()[0]['id'];
        $page = $this->findById($id);
        return $page;
    }
}
?>