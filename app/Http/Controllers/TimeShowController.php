<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TimeShowController extends Controller
{
/**
 * 現在時刻を表示するページ
 *
 * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
 */
public function timeshow()
{
    $time = date("Y-m-d H:i:s");
    return view('timeshow', compact('time'));
}
}
