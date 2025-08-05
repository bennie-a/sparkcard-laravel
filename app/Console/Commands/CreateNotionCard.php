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
use App\Services\Constant\StockpileHeader;
use GuzzleHttp\Psr7\Header;

/**
 * セット略称を条件に各カードのNotionカードを一括作成するコマンドクラス
 */
class CreateNotionCard extends Command
{
    /*
     *
     * @var string
     */
    protected $signature = 'createcard {set : セット略称}{color? : W,R,B,U,G,A,M,Land,Lのいずれか(任意)}';

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
        $this->info("Notionカードを作成します。");
        $set = $this->argument(Con::SET);
        $color = $this->argument(Con::COLOR);

        logger()->debug('argument',[$set, $color]);

        $condition = [
            'card_info.name' => '',
            'e.attr' => $set,
            'card_info.isFoil' => 'false'];
        if (!is_null($color)) {
            $condition['card_info.color_id'] = $color;
        }
        $result = CardInfo::fetchByCondition($condition);
        $total = count($result);
        $this->info(sprintf("カード情報を%s件を取得しました。", $total));
        $this->info("******************************************");
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        $details = [Con::QUANTITY => '0', Con::MARKET_PRICE => '0', 'condition' => 'NM', 'language' => 'JP'];

        $createCount = 0;
        $createList = [];
        $skippedCount = 0;

        foreach($result as $r) {
            if (CardBoard::exists($r->id)) {
                $skippedCount++;
                $bar->advance();
                continue;
            }
            $details[StockpileHeader::CARD_ID] = $r->id;
            CardBoard::store($details);
            $promotype = !empty($r->promo_name) ? "≪{$r->promo_name}≫" : $r->promo_name;
            $createList[] = $r->name.$promotype;
            $createCount++;
            $bar->advance();
        }
        $bar->finish();
        $this->info("******************************************");
        $this->info("Notionカードの作成が完了しました。");
        $this->info("✅ 作成件数: {$createCount}");
        $this->info("⚠️ スキップ件数: {$skippedCount}");

        if ($createCount > 0) {
            $this->info("作成カード名一覧:");
            foreach ($createList as $c) {
                $this->line(" -  {$c}");
            }
        }

        return Command::SUCCESS;
    }
}
