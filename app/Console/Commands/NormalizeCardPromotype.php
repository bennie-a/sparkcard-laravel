<?php

namespace App\Console\Commands;

use App\Models\Expansion;
use App\Models\Promotype;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant as GCon;
use Illuminate\Console\Command;

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
        $com = Expansion::findBySetCode('COM');
        $defaultPromotype = Promotype::findCardByAttr('draft');
        $this->info('デフォルトのプロモタイプ: ' . $defaultPromotype);
        $this->newLine(2);
        $this->info('プロモタイプの分離が完了しました。');
    }
}
