<?php

namespace App\Console\Commands;

use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Promotype;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant as GCon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**card_infoテーブルのnameのうち、カード名とプロモタイプに分離する。
 * プロモタイプは、カード名の後ろに「≪プロモタイプ≫」の形式で付与されている。
 */
class NormalizeCardPromotype extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'normalize:card-promotype';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'card_info.nameからプロモタイプを分離し、promotype_idに正規化する';
    
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('カード名からプロモタイプの分離を開始します…');
        $defaultPromotype = Promotype::findCardByAttr('draft');

        $total = Db::table('card_info')->whereNull(CardConstant::PROMO_ID)->count();
        $this->info("カード情報の総数: {$total}");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $updatedCount = 0;
        $skippedCount = 0;
        $skippedList = [];


        DB::table('card_info')->whereNull(CardConstant::PROMO_ID)
        ->orderBy(GCon::ID)
        ->chunk(100, function ($cards) use ($bar, $defaultPromotype, &$updatedCount, &$skippedCount, &$skippedList) {
            foreach ($cards as $card) {
                $name = $card->name;
                $cardName = $name;
                $promotypeName = null;
                
                if ($card->color_id === 'T') {
                    $promotype = $defaultPromotype;
                } else if (preg_match('/(.+?)≪(.+?)≫$/u', $name, $matches)) {
                    // プロモタイプの抽出
                    $cardName = trim($matches[1]);
                    $promotypeName = trim($matches[2]);

                    $promotype = Promotype::findCardByName($promotypeName);
                    if (!$promotype) {
                        $attr = base_convert(mt_rand(pow(36, 8 - 1), pow(36,8) - 1), 10, 36);
                        $item = [CardConstant::ATTR => $attr, GCon::NAME=> $promotypeName,  CardConstant::EXP_ID => $card->exp_id];
                        $promotype = Promotype::create($item);
                    }
                } else {
                    // プロモタイプが見つからない場合は、デフォルトのプロモタイプを使用
                    $promotype = $defaultPromotype;
                }

                // 更新処理
                DB::table('card_info')->where(GCon::ID, $card->id)->update([
                    GCon::NAME => $cardName,
                    CardConstant::PROMO_ID => $promotype->id,
                    'updated_at' => now(),
                ]);
                $updatedCount++;
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ 更新成功件数: {$updatedCount}");
        $this->info("⚠️ スキップ件数（プロモタイプが存在しない）: {$skippedCount}");

        if ($skippedCount > 0) {
            $this->warn("スキップされたカード名一覧:");
            foreach ($skippedList as $skipped) {
                $this->line(" - ID:{$skipped[GCon::ID]}, カード名: {$skipped[GCon::NAME]}");
            }
        }


        $this->info('プロモタイプの分離が完了しました。');
    }
}
