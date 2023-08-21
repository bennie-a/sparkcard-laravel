<?php

namespace App\Console\Commands;

use App\Models\CardInfo;
use App\Models\Foiltype;
use Illuminate\Console\Command;
/**
 * 表面加工カラムの値を追加するバッチクラス
 */
class TreatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'treat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'card_infoテーブルのtreat_idの値を変更する';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        logger()->info('Start Update Treatment');
        $list = CardInfo::where('name', 'like', '%Foil%')->get();
        foreach($list as $info) {
            logger()->info('name', [$info->name]);
            $foiltype = $this->findBracketsWord($info->name, "≪", "≫");
            $result = Foiltype::findByName($foiltype);
            if (!empty($result)) {
                $sandc = "≪S&C・Foil≫";
                if (strpos($info->name, $sandc) > 0) {
                    $info->name = str_replace($sandc, "≪ボーダレス「胆液」ショーケース≫", $info->name);
                }
                // logger()->debug('結果', [$info->number, $info->name, $result->name]);
            } else {
                $foiltype = $this->findBracketsWord($info->name, "【", "】");
                $trims = ["【エッチングFoil】", "【S&C・Foil】"];
                foreach ($trims as $t) {
                    if (strpos($info->name, $t) > 0) {
                        $info->name = str_replace($t, "", $info->name);
                    }
                }
                $result = Foiltype::findByName($foiltype);
                // logger()->debug('結果', [$info->number, $info->name, $foiltype]);
            }
            if (empty($foiltype)) {
                logger()->debug('Foiltypeなし', [$info->number, $info->name]);
                continue;
            }
            $info->foiltype_id = $result->id;
            $info->update();
        }
        // 【エッチングFoil】、⇒トル
        // ≪S&C・Foil≫⇒'ボーダレス「胆液」ショーケース
        // 

        logger()->info('Foil Card Count:'.count($list));
        return Command::SUCCESS;
    }

    private function findBracketsWord(string $word, string $start, string $end) {
        \preg_match("/(?<=".$start.")(.*?)(?=".$end.")/",$word, $match);
        return empty($match) ? null : current($match);
    }
}
