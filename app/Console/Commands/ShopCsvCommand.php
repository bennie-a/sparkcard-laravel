<?php

namespace App\Console\Commands;

use App\Facades\CardInfoServ;
use Illuminate\Console\Command;
use App\Services\Constant\CardConstant as Con;

/**
 * 商品CSVをDLするコマンドクラス
 */
class ShopCsvCommand extends Command
{
    /*
     *
     * @var string
     */
    protected $signature = 'shopcsv {set : セット略称} {color : W,R,B,U,G,A,M,Land,Lのいずれか}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'BASE・メルカリShops用CSVファイルを作成する。';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $set = $this->argument(Con::SET);
        $color = $this->argument(Con::COLOR);
        logger()->debug('引数',[$set, $color]);

        $info = CardInfoServ::fetch([Con::NAME => '', Con::SET => $set, Con::COLOR => $color, Con::IS_FOIL => 'false']);
        logger()->info('get card info', ['count' => count($info)]);

        return Command::SUCCESS;
    }
}
