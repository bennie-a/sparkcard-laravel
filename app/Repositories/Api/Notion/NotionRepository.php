<?php
namespace App\Repositories\Api\Notion;

use FiveamCode\LaravelNotionApi\Notion;

class NotionRepository {

    protected $databaseId;
    public function __construct($databaseId)
    {
        $this->databaseId = $databaseId;
    }
    
    public function store(array $details) {
    }

    public function findAll() {

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