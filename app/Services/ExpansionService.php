<?php
namespace App\Services;
use App\Repositories\Api\Notion\ExpansionRepository;
use App\Models\notion\NotionExp;

class ExpansionService {
    public function __construct() {
        $this->repo = new ExpansionRepository();
    }

    public function findAll() {
        $contents = $this->repo->findAll();
        $resultList = array();

        foreach($contents as $page) {
            $exp = new NotionExp();
            $array = $page->toArray();
            $exp->setNotionId($array['id']);
            $exp->setName($array['title']);
            $properties = $array['rawProperties'];
            $attrArray = $properties['略称']['rich_text'];
            logger()->debug($attrArray);
            $exp->setAttr($attrArray[0]['plain_text']);
            array_push($resultList, $exp);
        }
        return $resultList;
    }
}
?>