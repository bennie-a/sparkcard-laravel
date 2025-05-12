<?php

namespace App\Http\Controllers\Handler;

use App\Exceptions\api\NotFoundException;
use Illuminate\Http\Response;

class APIHandler {

    public function handleSearch(array $details, callable $fetchMethod, ?callable $transformer) {
        logger()->info('Start to search condition:', $details);
        $results = $fetchMethod($details);
        if ($results->isEmpty()) {
            logger()->info('No Result');
            throw new NotFoundException();
        }
        $count = $results->count();
        logger()->info("End to search Result Count:$count");
        return response($transformer($results), Response::HTTP_OK);
    }
}