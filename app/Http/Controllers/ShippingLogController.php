<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ImportCsv;
use Illuminate\Http\Request;
/**
 * 出荷ログAPI
 */
class ShippingLogController extends Controller
{
    use ImportCsv;
}
