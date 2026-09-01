<?php

namespace App\Enum;

/**
 * eloquentの順序を示すenumクラス
 */
enum SortOrder: string
{
    case ASC = 'asc';
    case DESC = 'desc';
}
