<?php

namespace App\Enum;

/**
 * CSVの処理フローを表す列挙型
 */
enum CsvFlowType: string
{
    case ARRIVAL = 'arrival';   // 入荷
    case SHIPT = 'shipt'; // 出荷

    /**
     * 値の配列を返す
     *
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(fn(self $c) => $c->value, self::cases());
    }
}