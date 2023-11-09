<?php

namespace App\Console\Commands;

use App\Enum\CardLanguage;
use App\Facades\CardBoard;
use App\Facades\CardInfoServ;
use App\Files\Item\BaseCsvWriter;
use App\Files\Item\MercariCsvWriter;
use App\Models\CardInfo;
use Illuminate\Console\Command;
use App\Services\Constant\CardConstant as Con;

/**
 * セット略称を条件に各カードのNotionカードを一括作成するコマンドクラス
 */
class CreateNotionCard extends Command
{
    /*
     *
     * @var string
     */
    protected $signature = 'createcard {set : セット略称}{color : W,R,B,U,G,A,M,Land,Lのいずれか}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '商品管理ボードに新商品を一括作成する。';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        logger()->info('start createcard.');
        $set = $this->argument(Con::SET);
        $color = $this->argument(Con::COLOR);

        logger()->debug('argument',[$set, $color]);

        $condition = [
            'card_info.name' => '',
            'card_info.color_id' => $color,
            'e.attr' => $set,
            'card_info.isFoil' => 'false'];
        $result = CardInfo::fetchByCondition($condition);
        logger()->info('get card info', ['count' => count($result)]);
        $details = [Con::QUANTITY => '0', Con::MARKET_PRICE => '0', 'condition' => 'NM', 'language' => 'JP'];
        foreach($result as $r) {
            CardBoard::store($r, $details);
        }
        logger()->info('end createcard');
        return Command::SUCCESS;
    }
}
