<?php
namespace App\Enum;

/** 言語に関するenum */
enum CardLanguage:string {
    case JP = 'JP';
    case EN = 'EN';
    case IT = 'IT';
    case CS = 'CS';
    case CT = 'CT';
    case UNDEFINED = 'UNDEFINED';

    public function text() {
        return match($this){
            self::JP => '日本語',
            self::EN => '英語',
            self::IT => 'イタリア語',
            self::CS => '簡体中国語',
            self::CT => '繫体中国語',
            self::UNDEFINED => '不明'
        };
    }

    public static function find(string $language) {
        return CardLanguage::tryFrom($language) ?? CardLanguage::UNDEFINED;
    }

    /**
     * 言語名から言語略称を取得する。
     *
     * @param string $name
     * @return string
     */
    public static function reverse(string $name) {
        $langcase = self::UNDEFINED;
        switch($name) {
            case '日本語':
                $langcase = self::JP;
                break;
            case  '英語':
                $langcase = self::EN;
                break;
            case 'イタリア語':
                $langcase = self::IT;
                break;
            case '簡体中国語':
                $langcase = self::CS;
                break;
            case '繫体中国語':
                $langcase = self::CT;
                break;
        }
        return $langcase->name;
    }
}