<?php
namespace App\Services;

use App\Models\Expansion;
use App\Repositories\Api\Notion\ExpansionRepository;
use App\Models\notion\NotionExp;
use App\Services\ScryfallService;
use Illuminate\Support\Facades\DB;

class ExpansionService {
    public function __construct() {
        $this->repo = new ExpansionRepository();
    }

    public function findAll() {
        $contents = $this->repo->findAll();
        $resultList = array();
        $scryfallService = new ScryfallService();
        foreach($contents as $page) {
            $array = $page->toArray();
            $id = $array['id'];
            $name = $array['title'];
            $isExist = Expansion::isExist($name);
            if ($isExist) {
                continue;
            }
            $exp = new NotionExp();

            $exp->setNotionId($id);
            $exp->setName($name);
            logger()->debug("エキスパンション：".$id.":".$exp->getName());

            $properties = $array['rawProperties'];

            // 略称
            $attrArray = $properties['略称']['rich_text'];
            $attr = $attrArray[0]['plain_text'];
            $exp->setAttr($attr);

            //BASE_ID
            $baseId = $page->getProperty("BASEID");
            if (!is_null($baseId)) {
                $exp->setBaseId($baseId->getContent());
            } 

            // リリース日
            $release = $scryfallService->getReleaseDate($attr);
            $exp->setRelaseAt($release);
            array_push($resultList, $exp);
        }
        return $resultList;
    }
}
?>