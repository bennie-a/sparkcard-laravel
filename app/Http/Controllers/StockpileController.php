<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ImportCsv;
use Illuminate\Http\Request;
/**
 * 在庫管理機能API
 */
class StockpileController extends Controller
{
    use ImportCsv;
}
