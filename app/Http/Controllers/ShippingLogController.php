<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\stock\ShippingLogService;
use App\Traits\ImportCsv;
use Illuminate\Http\Request;
/**
 * 出荷ログAPI
 */
class ShippingLogController extends Controller
{
    public function __construct(ShippingLogService $service) {
        $this->service = $service;
    }

    use ImportCsv;
    
}
