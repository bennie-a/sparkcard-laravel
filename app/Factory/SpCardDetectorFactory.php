<?php
namespace App\Factory;

use App\Services\interfaces\SpCardDetectorInterface;
use App\Services\Specific\DefaultSpCardDetector;
use App\Services\Specific\DftCardDetector;
use App\Services\Specific\DskSpCardDetector;
use App\Services\Specific\Mh3SpCardDetector;
use App\Services\Specific\TdmSpCardDetector;
use App\Services\Specific\WoeSpCardDetector;

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
            "WOE" => WoeSpCardDetector::class,
            "DFT" => DftCardDetector::class,
            "TDM" => TdmSpCardDetector::class,
            default => DefaultSpCardDetector::class
        };
        return new $class;
    }
}