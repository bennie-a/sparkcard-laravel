<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CsvFileRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TranslateController extends Controller
{
    public function index(CsvFileRequest $request) {
        $path = $request->input('path');
        return response()->json([], Response::HTTP_OK);
    }
}
