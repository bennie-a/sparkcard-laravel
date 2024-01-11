<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
/**
 * 売り上げをやよい青色申告に一括登録するCSVファイルを作成するコマンドクラス
 */
class SalesTaxCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'taxcsv {--mercari : メルカリの売上のみ作成する} {--base : BASEの売上のみ作成する}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'やよい青色申告に売掛金を一括登録するCSVファイルを作成する。';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isMercari = $this->option("mercari");
        $isBase = $this->option('base');
        $Shop = 'メルカリShops';
        if (!$isMercari && $isBase) {
            $Shop = 'BASEショップ';
        }
        if ($this->confirm($Shop.'の売上のみ作成してもよろしいでしょうか?')) {

        }
        $this->info('処理を終了しました。');
        return config('command.exit_code.SUCCESS');
    }
}
