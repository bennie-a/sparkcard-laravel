<?php
namespace App\Factory;

use App\Models\CardInfo;
use FiveamCode\LaravelNotionApi\Entities\Page;

class NotionPageFactory {

    public function create($id, $details):Page
    {

        $page = new Page();
        if (empty($id) == false) {
            $page->setId($id);
        }
        $setStatus = function($page, $value) {
            $page->setSelect("Status", $value);
        };
        $setImageUrl = function($page, $value) use($details){
            $info = CardInfo::findCardByAttr($details['attr'], $value);
            logger()->debug($info);
            $page->setUrl('画像URL', $info->image_url);
        };
        $setAttr = function($page, $value) {
            // 略称に関しては何もしない
        };
        $detailMap = ['status' => $setStatus, 'name' => $setImageUrl, 'attr' => $setAttr];
        // 入力パラメータに沿って値を設定する。
        foreach($details as $key => $value) {
            $func = $detailMap[$key];
            $func($page, $value);
        }
        
        return $page;
    }

}