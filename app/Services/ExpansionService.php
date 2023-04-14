<?php
namespace App\Services;

use App\Models\Expansion;
use App\Repositories\Api\Notion\ExpansionRepository;
use App\Models\notion\NotionExp;
use App\Services\ScryfallService;
use Illuminate\Support\Facades\DB;
use FiveamCode\LaravelNotionApi\Entities\Page;

/**
 * Notionのエキスパンション一覧を操作するクラス
 */
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

    /**
     * エキスパンションを1件登録する。
     *
     * @return stirng エキスパンションID
     */
    public function store(array $details) {
        // 重複チェック。
        // エラーなら例外発生
        // Notionに登録
        $page = new Page();
        $page->setTitle('名前', $details['name']);
        $page->setText('略称', $details['attr']);
        $page->setSelect('ブロック', 'sss');
        $page->setSelect('フォーマット', 'ddd');
        $page = $this->repo->store($page);
        logger()->debug('ID:'.$page->getId());
        return $page->getId();
    }
}
?>