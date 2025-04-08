<?php
namespace App\Traits;

/**UUIDのバリデーションルール */
trait UuidRules
{
    public static function uuidrules(): string
    {
        return 'regex:/([0-9a-f]{8})-([0-9a-f]{4})-([0-9a-f]{4})-([0-9a-f]{4})-([0-9a-f]{12})/';
    }
}
