<?php

namespace App\Http\Controllers\Handler;

use App\Exceptions\api\NoContentException;
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

    public function handleShow(int $id, callable $fetchMethod, ?callable $transformer) {
        logger()->info("Start to Find id:{$id}");
        $model = $fetchMethod($id);
        if (!$model) {
            logger()->info('No Result');
            throw new NoContentException();
        }
        logger()->info("End to Find id:{$id}");
        return response($transformer($model), Response::HTTP_OK);
    }

    /**
     * 情報を1件更新する。
     *
     * @param integer $id
     * @param array $details
     * @param callable $fetchMethod
     * @param callable|null $transformer
     * @return void
     */
    public function handleUpdate(int $id, array $details, callable $fetchMethod, callable $updateMethod, callable $transformer) {
    }
}