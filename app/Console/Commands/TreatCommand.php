<?php

namespace App\Console\Commands;

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
        return Command::SUCCESS;
    }
}
