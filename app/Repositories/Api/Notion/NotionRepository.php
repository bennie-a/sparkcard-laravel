<?php
namespace App\Repositories\Api\Notion;

use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Exceptions\NotionException;
use FiveamCode\LaravelNotionApi\Notion;
use FiveamCode\LaravelNotionApi\Query\Filters\Filter;
use FiveamCode\LaravelNotionApi\Query\Filters\FilterBag;
use FiveamCode\LaravelNotionApi\Query\Filters\Operators;
use FiveamCode\LaravelNotionApi\Query\StartCursor;
use Illuminate\Support\Collection;

class NotionRepository {

    protected $databaseId;
    public function __construct($databaseId)
    {
        $this->databaseId = $databaseId;
    }
    
    /**
     * Notionのページ情報を1件登録する。
     *
     * @param Page $page
     * @return $page 登録後のカード情報(IDとURLが取得できる)
     */
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

    public function update(Page $page) {
        try {
            $notion = self::createNotion();
            $notion->pages()->update($page);
            logger()->info('更新完了:'.$page->getId());
        } catch(NotionException $e) {
            logger()->error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 特定のNotionページを1件取得する。
     *
     * @param string $prop 項目名
     * @param string $value 値
     * @return void
     */
    public function findByEquals(string $prop, string $value) {
        $filters = new Collection();
        $filters->add(Filter::textFilter($prop, Operators::EQUALS, $value));
        return $this->findOnebyFilter($filters);
    }

    /**
     * Notionページを1件だけ取得する。
     *
     * @param Collection $filters
     * @return Page Notionページ
     */
    protected function findOnebyFilter(Collection $filters) {
        $pages = $this->findAsCollection($filters);
        $p = $pages[0];
        return $p;
    }

    protected function findAsCollection(Collection $filters) {
        $pages = $this->findByQuery($filters);
        return $pages->asCollection();
    }

    protected function findByQuery(Collection $filters) {
        $notion = self::createNotion();
        $pages = $notion->database($this->databaseId)->filterBy($filters)->query();
        return $pages;
    }

    protected function createEqualFilter(string $prop, $value) {
        $filters = new Collection();
        $filters->add(Filter::textFilter($prop, Operators::EQUALS, $value));
        return $filters;
    }

    public function findByNextCursor($nextCursor) {
        $notion = self::createNotion();
        $startCursor = new StartCursor($nextCursor);
        $pages = $notion->database($this->databaseId)->offset($startCursor);
        return $pages;
    }

    // idから特定のページを取得する。
    public function findById($id) {
        $notion = self::createNotion();
        $page = $notion->pages()->find($id);
        return $page;
    }

    // Notionオブジェクトを作成する。
    private function createNotion() {
        $token = config("notion.token");
        $notion = new Notion($token);
        return $notion;
    }

    protected function getDatabase() {
        $notion = self::createNotion();
        return $notion->database($this->databaseId);
    }
}
?>