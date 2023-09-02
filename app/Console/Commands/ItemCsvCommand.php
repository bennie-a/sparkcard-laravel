<?php

namespace App\Console\Commands;

use App\Facades\CardInfoServ;
use App\Files\Item\BaseCsvWriter;
use App\Files\Item\MercariCsvWriter;
use Illuminate\Console\Command;
use App\Services\Constant\CardConstant as Con;

/**
 * 商品CSVをDLするコマンドクラス
 */
class ItemCsvCommand extends Command
{
    /*
     *
     * @var string
     */
    protected $signature = 'itemcsv {set : セット略称} {color : W,R,B,U,G,A,M,Land,Lのいずれか}';

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
        logger()->info('start download csv.');
        $set = $this->argument(Con::SET);
        $color = $this->argument(Con::COLOR);
        logger()->debug('argument',[$set, $color]);

        $result = CardInfoServ::fetch([Con::NAME => '', Con::SET => $set, Con::COLOR => $color, Con::IS_FOIL => 'false']);
        logger()->info('get card info', ['count' => count($result)]);
        $files = [new BaseCsvWriter(), new MercariCsvWriter()];
        foreach($files as $writer) {
            logger()->info('write item csv:'.$writer->shopname());
            $writer->write($result);
        }
        logger()->info('end download csv.');
        return Command::SUCCESS;
    }
}
