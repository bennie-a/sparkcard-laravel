<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * 各ネットショップの在庫CSVファイルから商品IDをDBに登録する。
 */
class SyncProductIDsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncproductid {filename} {--mercari : メルカリの売上のみ作成する} {--base : BASEの売上のみ作成する}';

    protected $description = '各ショップの商品IDをDBに登録する。';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isMercari = $this->option("mercari");
        $isBase = $this->option('base');

        if (($isMercari && $isBase) || (!$isMercari && !$isBase) ) {
            $this->error("--mercariか--baseのどちらかを付けてください。");
            return config('command.exit_code.ERROR');
        }
        $this->info("登録終了しました。");
        return config('command.exit_code.SUCCESS');
    }
}
