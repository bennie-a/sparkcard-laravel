<?php
namespace App\Enum;
use App\Api\Client\AbstractApiClient;
use App\Api\Client\ScryfallClient;

/**
 * 外部APIの種類Enum
 */
enum ExternalApi: string
{
    // case BASE = 'base';
    // case WISDOM_GUILD = 'wisdom';
    case SCRYFALL = 'scryfall';
    // case MTGDEV = 'mtgdev';

    /**
     * 外部API別の接続クラスを取得する。
     *
     * @return AbstractApiClient
     */
    public function client(): AbstractApiClient
    {
        return match($this) {
            // self::BASE => new \App\Api\Client\BaseClient(),
            // self::WISDOM_GUILD => new \App\Api\Client\WisdomGuildClient(),
            self::SCRYFALL => new ScryfallClient(),
            // self::MTGDEV => new \App\Api\Client\MtgDevClient(),
        };
    }
}
