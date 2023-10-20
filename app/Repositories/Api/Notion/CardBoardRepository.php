<?php
namespace App\Repositories\Api\Notion;
use App\Repositories\Api\Notion\NotionRepository;
use FiveamCode\LaravelNotionApi\Endpoints\Database;
use FiveamCode\LaravelNotionApi\Entities\Collections\PageCollection;
use FiveamCode\LaravelNotionApi\Exceptions\NotionException;
use FiveamCode\LaravelNotionApi\Notion;
use FiveamCode\LaravelNotionApi\Query\Filters\Filter;
use FiveamCode\LaravelNotionApi\Query\Filters\FilterBag;
use FiveamCode\LaravelNotionApi\Query\Filters\Operators;
use FiveamCode\LaravelNotionApi\Query\Sorting;
use Illuminate\Support\Collection;

//Notionのエキスパンションテーブルへの接続
class CardBoardRepository extends NotionRepository{

    public function __construct()
    {
        $databaseId = config('notion.cardboard');
        parent::__construct($databaseId);
    }

    // Statusと一致するカード情報を取得する。
    public function findByStatus(string $status, int $price) {
        $filterbag = FilterBag::and();
        $filterbag->addFilter(new Filter("Status", 'select', [Operators::EQUALS => $status]));
        $filterbag->addFilter(Filter::numberFilter("価格", Operators::GREATER_THAN_OR_EQUAL_TO, $price));

        // ソート順設定
        $sorting  = Sorting::propertySort("カード番号", "ascending");
        $database = $this->getDatabase();
        // $database = $notion->database($this->databaseId)->filterBy($filters)->sortBy($sorting);
        $pageCollection = $database->filterBy($filterbag)->sortBy($sorting)->query();
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

    public function findByOrderId(string $orderId) {
        $filter = Filter::textFilter('注文番号', Operators::EQUALS, $orderId);
        $pages = $this->getDatabase()->filterBy($filter)->query()->asCollection();
        return $pages[0];
    }

    public function findBySparkcardId (int $id) {
        $filter = Filter::numberFilter('sparkcard_id', Operators::EQUALS, $id);
        $pages = $this->getDatabase()->filterBy($filter)->query()->asCollection();
        if ($pages->isEmpty()) {
            return null;
        }
        return $pages[0];
    }
}
?>