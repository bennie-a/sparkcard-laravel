<?php
namespace App\Repositories\Api\Notion;
use App\Repositories\Api\Notion\NotionRepository;
use FiveamCode\LaravelNotionApi\Endpoints\Database;
use FiveamCode\LaravelNotionApi\Entities\Collections\PageCollection;
use FiveamCode\LaravelNotionApi\Notion;
use FiveamCode\LaravelNotionApi\Query\Filters\Filter;
use FiveamCode\LaravelNotionApi\Query\Filters\Operators;
use Illuminate\Support\Collection;

//Notionのエキスパンションテーブルへの接続
class CardBoardRepository extends NotionRepository{

    public function __construct()
    {
        $databaseId = config('notion.cardboard');
        parent::__construct($databaseId);
    }

    // Statusと一致するカード情報を取得する。
    public function findByStatus($status) {
        $filters = new Collection();
        $filter = new Filter("Status", 'select', [Operators::EQUALS => $status]);
        $filters->add($filter);
        $notion = self::createNotion();
        $database = $notion->database($this->databaseId)->filterBy($filters);
        $pageCollection = $database->query();
        $pages = $pageCollection->asCollection();

        $pages = $this->getCardCollection($database, $pageCollection, $pages);
        return $pages;
    }
    
    private function getCardCollection(Database $database, PageCollection $pageCollection, Collection $pages) {
        if ($pageCollection->hasMoreEntries()) {
            $nextCollection = $database->offsetByResponse($pageCollection)->query();
            $nextPage = $this->getCardCollection($database, $nextCollection, $nextCollection->asCollection());
            foreach($nextPage as $next) {
                $pages->push($next);
            }
        }
        return $pages;
    }


    public function findByExp($name) {
        $repo = new ExpansionRepository();
        $expId = $repo->findIdByName($name);
        $filter =$this->createEqualFilter("エキスパンション", $expId);
        $result = $this->findAsCollection($filter);
        return $result;
    }
}
?>