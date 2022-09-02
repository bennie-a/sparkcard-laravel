<?php
namespace App\Repositories\Api\Notion;
use App\Repositories\Api\Notion\NotionRepository;

//Notionのエキスパンションテーブルへの接続
class CardBoardRepository extends NotionRepository{

    public function __construct()
    {
        $databaseId = config('notion.cardboard');
        parent::__construct($databaseId);
    }
}
?>