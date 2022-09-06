<?php
namespace App\Repositories\Api\Notion;

use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Exceptions\NotionException;
use FiveamCode\LaravelNotionApi\Notion;
use FiveamCode\LaravelNotionApi\Query\Filters\Filter;
use FiveamCode\LaravelNotionApi\Query\Filters\Operators;
use Illuminate\Support\Collection;

class NotionRepository {

    protected $databaseId;
    public function __construct($databaseId)
    {
        $this->databaseId = $databaseId;
    }
    
    public function store(Page $page) {
        try {
            $notion = self::createNotion();
            $page = $notion->pages()->createInDatabase($this->databaseId, $page);
            return $page;
        } catch(NotionException $e) {
            logger()->error($e->getMessage());
            throw $e;
        }
    }

    public function findAll() {

    }

    public function findByEquals(string $prop, string $value) {
        $filters = new Collection();
        $filters->add(Filter::textFilter($prop, Operators::EQUALS, $value));
        return $this->findOnebyFilter($filters);
    }

    protected function findOnebyFilter(Collection $filters) {
        $notion = self::createNotion();
        $pages = $this->findAsCollection($filters);
        $p = $pages[0];
        return $p;
    }

    protected function findAsCollection(Collection $filters) {
        $notion = self::createNotion();
        $pages = $notion->database($this->databaseId)->filterBy($filters)->query()->asCollection();
        return $pages;
    }

    protected function createEqualFilter(string $prop, $value) {
        $filters = new Collection();
        $filters->add(Filter::textFilter($prop, Operators::EQUALS, $value));
        return $filters;
    }

    // Notionオブジェクトを作成する。
    public static function createNotion() {
        $token = config("notion.token");
        $notion = new Notion($token);
        return $notion;
    }

    protected function getDatabaseId() {
        return $this->databaseId;
    }
}
?>