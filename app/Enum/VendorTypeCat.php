<?php
namespace App\Enum;
/**
 * 取引先カテゴリのEnumクラス
 */
enum VendorTypeCat: int
{
    case ORIGINAL_PACK = 1; // オリジナルパック
    case PERSONAL       = 2; // 私物
    case PURCHASE       = 3; // 買取
    case INVENTORY      = 4; // 棚卸し
    case RETURNED       = 5; // 返品

    public function text(): string
    {
        return match($this) {
            self::ORIGINAL_PACK => 'オリジナルパック',
            self::PERSONAL      => '私物',
            self::PURCHASE      => '買取',
            self::INVENTORY     => '棚卸し',
            self::RETURNED      => '返品',
        };
    }

    public static function find(int $id): ?VendorTypeCat
    {
        return VendorTypeCat::tryFrom($id);
    }

    /**
     * 取引先カテゴリの日本語名を取得する。
     *
     * @return array
     */
    public static function toTextArray(): array
    {
        return array_map(
            fn(self $case) => $case->text(),
            self::cases()
        );
    }
}
