<?php

namespace App\Console\Commands;

use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Promotype;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant as GCon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * shipping_logテーブルの内容をordersテーブルとorder_itemテーブルに
 * 分割するコマンド。
 */
class DivideOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'divide:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'shipping_logテーブルの内容をordersテーブルとorder_itemテーブルに分割する';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('注文情報の分割を開始します。');
        // $defaultPromotype = Promotype::findCardByAttr('draft');

        // $total = Db::table('card_info')->whereNull(CardConstant::PROMO_ID)->count();
        // $this->info("カード情報の総数: {$total}");

        // $bar = $this->output->createProgressBar($total);
        // $bar->start();

        // $updatedCount = 0;
        // $skippedCount = 0;
        // $skippedList = [];


        // DB::table('card_info')->whereNull(CardConstant::PROMO_ID)
        // ->orderBy(GCon::ID)
        // ->chunk(100, function ($cards) use ($bar, $defaultPromotype, &$updatedCount, &$skippedCount, &$skippedList) {
        //     foreach ($cards as $card) {
        //         $name = $card->name;
        //         $cardName = $name;
        //         $promotypeName = null;

        //         if ($card->color_id === 'T') {
        //             $promotype = $defaultPromotype;
        //         } else if (preg_match('/(.+?)≪(.+?)≫$/u', $name, $matches)) {
        //             // プロモタイプの抽出
        //             $cardName = trim($matches[1]);
        //             $promotypeName = trim($matches[2]);

        //             $promotype = Promotype::findCardByName($promotypeName);
        //             if (!$promotype) {
        //                 $attr = base_convert(mt_rand(pow(36, 8 - 1), pow(36,8) - 1), 10, 36);
        //                 $item = [CardConstant::ATTR => $attr, GCon::NAME=> $promotypeName,  CardConstant::EXP_ID => $card->exp_id];
        //                 $promotype = Promotype::create($item);
        //             }
        //         } else {
        //             // プロモタイプが見つからない場合は、デフォルトのプロモタイプを使用
        //             $promotype = $defaultPromotype;
        //         }

        //         // 更新処理
        //         DB::table('card_info')->where(GCon::ID, $card->id)->update([
        //             GCon::NAME => $cardName,
        //             CardConstant::PROMO_ID => $promotype->id,
        //             'updated_at' => now(),
        //         ]);
        //         $updatedCount++;
        //         $bar->advance();
        //     }
        // });

        // $bar->finish();
        // $this->newLine(2);
        // $this->info("✅ 更新成功件数: {$updatedCount}");
        // $this->info("⚠️ スキップ件数（プロモタイプが存在しない）: {$skippedCount}");

        // if ($skippedCount > 0) {
        //     $this->warn("スキップされたカード名一覧:");
        //     foreach ($skippedList as $skipped) {
        //         $this->line(" - ID:{$skipped[GCon::ID]}, カード名: {$skipped[GCon::NAME]}");
        //     }
        // }


        $this->info('注文情報の分割が完了しました。');
    }
}
