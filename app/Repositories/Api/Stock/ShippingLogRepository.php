<?php
namespace App\Repositories\Api\Stock;

use App\Models\ShippingLog;
use Illuminate\Support\Facades\DB;

class ShippingLogRepository {

    /**
     * DBから注文番号に該当する出荷情報を取得する。
     *
     * @param string $orderId
     * @return void
     */
    public function fetchByOrderId(string $orderId) {
        return ShippingLog::fetchByOrderId($orderId);
    }
}
