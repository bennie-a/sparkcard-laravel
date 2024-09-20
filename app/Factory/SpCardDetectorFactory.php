<?php
namespace App\Factory;

use App\Services\interfaces\SpCardDetectorInterface;
use App\Services\Specific\DefaultSpCardDetector;
use App\Services\Specific\DskSpCardDetector;
use App\Services\Specific\Mh3SpCardDetector;
use PhpParser\Builder\Class_;

class SpCardDetectorFactory {
    /**
     * エキスパンション別に特別版を特定するクラスを生成する。
     *
     * @return SpCardDetectorInterface
     */
    public static function create(string $setCode) {
        $class = match($setCode){
            "DSK" => DskSpCardDetector::class,
            "MH3" => Mh3SpCardDetector::class,
            default => DefaultSpCardDetector::class
        };
        return new $class;
    }
}