<?php
namespace App\Factory;
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
        $detailMap = ['status' => $setStatus];
        foreach($details as $key => $value) {
            $func = $detailMap[$key];
            $func($page, $value);
        }
        return $page;
    }

}