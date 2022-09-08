<?php
namespace App\Repositories\Api\Notion;
use App\Repositories\Api\Notion\NotionRepository;
use FiveamCode\LaravelNotionApi\Endpoints\Database;
use FiveamCode\LaravelNotionApi\Entities\Collections\PageCollection;
use FiveamCode\LaravelNotionApi\Notion;
use FiveamCode\LaravelNotionApi\Query\Filters\Filter;
use FiveamCode\LaravelNotionApi\Query\Filters\Operators;
use FiveamCode\LaravelNotionApi\Query\Sorting;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Constraint\Operator;

//Notionのエキスパンションテーブルへの接続
class CardBoardRepository extends NotionRepository{

    public function __construct()
    {
        $databaseId = config('notion.cardboard');
        parent::__construct($databaseId);
    }

    // Statusと一致するカード情報を取得する。
    public function findByStatus($status, $details) {
        $filters = new Collection();
        $filter = new Filter("Status", 'select', [Operators::EQUALS => $status]);
        $filters->add($filter);

        $notion = self::createNotion();

        // ソート順設定
        $sorting = new Collection();
        $sorting->add(Sorting::propertySort("カード番号", "ascending"));
        $database = $notion->database($this->databaseId)->filterBy($filters)->sortBy($sorting);
        $pageCollection = $database->query();
        $pages = $pageCollection->asCollection();

        $pages = $this->getCardCollection($database, $pageCollection, $pages);
        return $pages;
    }
    
    // next_cursorを元に100件目以降のカード情報を取得する。
    private function getCardCollection(Database $database, PageCollection $pageCollection, Collection $pages) {
        if ($pageCollection->hasMoreEntries()) {
            $nextCollection = $database->offsetByResponse($pageCollection)->query();
            $nextPage = $this->getCardCollection($database, $nextCollection, $nextCollection->asCollection());
            foreach($nextPage as $next) {
                $pages->add($next);
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