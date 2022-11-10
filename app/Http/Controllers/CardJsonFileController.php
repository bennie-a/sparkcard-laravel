<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\File;
use Illuminate\Http\Request;

class CardJsonFileController extends Controller
{
    public function upload(Request $request) {
        $path = $request->all();
        logger()->debug($path['meta']);
    }
}
?>