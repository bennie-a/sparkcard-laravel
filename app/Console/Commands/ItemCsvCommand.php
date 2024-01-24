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
    protected $signature = 'itemcsv {set : セット略称} {color : W,R,B,U,G,A,M,Land,Lのいずれか} {--start=:開始番号}{--excPromo=*}';

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
        $this->info('start download csv.');
        $set = $this->argument(Con::SET);
        $color = $this->argument(Con::COLOR);
        $start_number = (int)$this->option('start');
        $excPromo = $this->option('excPromo');

        logger()->debug('argument',[$set, $color, $start_number, $excPromo]);

        $result = CardInfoServ::fetch([Con::NAME => '', Con::SET => $set, Con::COLOR => $color, Con::IS_FOIL => 'false']);
        $filterd = $result->filter(function($row) use ($excPromo) {
            foreach($excPromo as $promo) {
                if (strpos($row->name, $promo)) {
                    return false;
                }
            }
            return true;        
        });

        $this->info('get card info.count:'.count($filterd));
        $files = [new BaseCsvWriter(), new MercariCsvWriter()];
        foreach($files as $writer) {
            $this->info('write item csv:'.$writer->shopname());
            $writer->write($set, $color, $start_number, $filterd);
        }
        $this->info('end download csv.');
        return Command::SUCCESS;
    }
}
