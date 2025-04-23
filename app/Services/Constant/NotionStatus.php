<?php
namespace App\Services\Constant;

/**
 * Notionの販売管理ボードのStatusクラス
 */
enum NotionStatus:string {
    case PhotoPending = '要写真撮影';
    case ToBeDeleted = '削除対象';
    case Complete = '取引完了';
}
