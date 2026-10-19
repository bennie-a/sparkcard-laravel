<?php

namespace App\Console\Commands;

use App\Enum\ShopPlatform;
use App\Enum\SortOrder;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Promotype;
use App\Models\ShippingLog;
use App\Models\Shipt\OrderItem;
use App\Models\Shipt\Orders;
use App\Repositories\Api\Shipt\ShiptLogRepository;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant as GCon;
use Illuminate\Console\Command;
use App\Services\Constant\ShiptConstant as SC;
use App\Services\Shipt\ShiptStoreRow;
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
        OrderItem::truncate();
        Orders::truncate();
        $this->info('注文情報の分割を開始します。');
        $shiptInfo = ShippingLog::all();
        $this->info("shipping_logテーブルの総件数: {$shiptInfo->count()}");

        $repo = new ShiptLogRepository();
        $bar = $this->output->createProgressBar($shiptInfo->count());
        $bar->start();

        DB::table('shipping_log')->orderBy('shipping_date', SortOrder::ASC->value)
        ->chunk(100, function ($shipts) use ($bar, $repo){
            foreach($shipts as $s) {
                $info = ShippingLog::where('order_id', $s->order_id)->get();
                $platform = ShopPlatform::MERCARI;
                $orders = $repo->findByOrderId($platform, $s->order_id);
                if (!isset($orders)) {
                    $this->info("注文番号({$s->order_id})の登録を開始します。");
                    $this->info("注文番号({$s->order_id})の商品数: {$info->count()}");
                    $row = [
                        SC::PLATFORM => $platform,
                        SC::ORDER_ID => $s->order_id,
                        SC::BUYER => $s->name,
                        SC::ZIPCODE => $s->zip_code,
                        SC::ADDRESS => $s->address,
                        SC::SHIPT_DATE => $s->shipping_date,
                        SC::ITEM_SUBTOTAL => $s->total_price,
                        SC::GRAND_TOTAL => $s->total_price,
                        SC::DISCOUNT_AMOUNT => 0,
                        SC::FEE => [GCon::ID => 1],
                        SC::ITEM_COUNT => $info->count()
                    ];
                    $shiptRow = new ShiptStoreRow($row);
                    $orders = $repo->createOrder($shiptRow);
                }

                $item = [
                                    SC::ORDER_ID => $s->order_id,
                                    GCon::ID => $s->stock_id,
                                    SC::SHIPMENT => $s->quantity,
                                    SC::UNIT_PRICE => $s->single_price,
                                    SC::SUBTOTAL => $s->total_price,
                ];
                $repo->createOrderItem($orders->id, $item);
                $this->info("注文番号({$s->order_id})の登録が完了しました。");
                $bar->advance();
            }
        });
        $bar->finish();
        $this->newLine(2);
        $ordersCount = Orders::query()->count();
        $this->info("✅ 登録成功件数: {$ordersCount}");

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
        return self::SUCCESS;
    }
}
