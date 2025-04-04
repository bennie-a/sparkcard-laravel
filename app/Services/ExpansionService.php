<?php
namespace App\Services;

use App\Exceptions\ConflictException;
use App\Libs\MtgJsonUtil;
use App\Models\Expansion;
use App\Repositories\Api\Notion\ExpansionRepository;
use App\Models\notion\NotionExp;
use App\Services\ScryfallService;
use FiveamCode\LaravelNotionApi\Entities\Page;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * エキスパンションを操作するクラス
 */
class ExpansionService {
    private $repo;
    public function __construct() {
        $this->repo = new ExpansionRepository();
    }

    public function fetch(string $query) {
        $columns = ['id', 'notion_id', 'name', 'attr', 'release_date'];
        $list = Expansion::where('attr', 'like', '%'.$query.'%')->limit(5)->get();
        return $list;
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
            // $baseId = $page->getProperty("BASEID");
            // if (!is_null($baseId)) {
            //     $exp->setBaseId($baseId->getContent());
            // } 

            // リリース日
            $release = $scryfallService->getReleaseDate($attr);
            $exp->setRelaseAt($release);
            array_push($resultList, $exp);
        }
        return $resultList;
    }

    public function isExistByAttr(string $attr) {
        return Expansion::isExistByAttr($attr);
    }

    /**
     * セット略称を元にScryfallで検索してDBに登録する。
     *
     * @param string $setcode
     * @param string $format
     * @return void
     */
    public function storeByScryfall(string $setcode, string $format) {
            // エキスパンション登録
            $contents = \App\Facades\ScryfallServ::findSet($setcode);
            $block = MtgJsonUtil::hasKey('block', $contents) ? $contents['block'] : 'その他';
            $details = ['attr' => strtoupper($setcode), 'name' => $contents['name'],
                                    'block' => $block, 'format' => $format,
                                    'release_date' => $contents['released_at']];
            $this->store($details);

    }
    /**
     * エキスパンションを1件登録する。
     *
     * @return stirng エキスパンションID
     */
    public function store(array $details) {
        $name = $details['name'];
        $attr = $details['attr'];

        // 重複チェック
        $isExist = Expansion::isExist($name);
        if ($isExist) {
            throw new ConflictException($name);
        }

        // Notionに登録
        $page = new Page();
        $page->setTitle('名前', $name);
        $page->setText('略称', $attr);
        $page->setSelect('ブロック', $details['block']);
        $page->setSelect('フォーマット', $details['format']);
        $page = $this->repo->store($page);
        logger()->debug('ID:'.$page->getId());

        // DBにエキスパンション一覧を登録する。
        $exp = new Expansion();
        $releaseDate = $details['release_date'];
        if (empty($releaseDate)) {
            $service = new ScryfallService();
            $releaseDate = $service->getReleaseDate($attr);
        }
        
        $carbon = new Carbon($releaseDate);
        $exp->create(['notion_id' => $page->getId(),
        'name' => $name,
        'attr' => $attr,
        'release_date' => $carbon]);
    }
}
?>