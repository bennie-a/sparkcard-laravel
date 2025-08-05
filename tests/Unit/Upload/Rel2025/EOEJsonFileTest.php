<?php
namespace Tests\Unit\Upload\Rel2025;

use Tests\Unit\Upload\AbstractCardJsonFileTest;
/**
 *EOEに関するテストクラス
 */
class EoeJsonFileTest extends AbstractCardJsonFileTest {
    
    public static function promoProvider() {
        return [
            // '「超常宇宙」' => ['311', 'surreal_space'],
            '「観測窓」' => ['277', 'observation']
        ];
    }

    public static function excludeprovider() {
        return [
            '星景ポスター' => ['372']
        ];
    }
    protected function getSetCode():string
    {
        return 'EOE';
    }
}