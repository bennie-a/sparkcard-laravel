<?php

namespace App\Console\Commands;

use App\Facades\CardBoard;
use App\Files\Csv\Tax\TaxCsvWriter;
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
        $shop = 'メルカリShops';
        if (!$isMercari && $isBase) {
            $shop = 'BASEショップ';
        }
        $result = CardBoard::findByTaxStatus($isMercari, $isBase);
        $this->info(sprintf("%sの売上%s件を取得しました。", $shop, count($result)));
        $this->info("指定したパスにCSVファイルを出力します。");
        $writer = new TaxCsvWriter();
        $writer->write($result);
        $this->info('処理を終了しました。');
        return config('command.exit_code.SUCCESS');
    }
}
