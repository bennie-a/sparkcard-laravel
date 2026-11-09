<?php

namespace App\Console\Commands;

use App\Enum\ShiptMethod;
use App\Enum\ShopPlatform;
use App\Enum\SortOrder;
use App\Exceptions\api\Shipt\NFShiptMethodException;
use App\Models\ShippingLog;
use App\Models\Shipt\OrderItem;
use App\Models\Shipt\Orders;
use App\Repositories\Api\Shipt\ShiptLogRepository;
use App\Services\Constant\GlobalConstant as GCon;
use Illuminate\Console\Command;
use App\Services\Constant\ShiptConstant as SC;
use App\Services\Shipt\ShiptMethodService;
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
    public function handle(ShiptMethodService $service)
    {
        OrderItem::truncate();
        Orders::truncate();
        $this->info('注文情報の分割を開始します。');
        $shiptInfo = ShippingLog::all();
        $this->info("shipping_logテーブルの総件数: {$shiptInfo->count()}");

        $repo = new ShiptLogRepository();
        $bar = $this->output->createProgressBar($shiptInfo->count());
        $bar->start();

        try {
            DB::table('shipping_log')->orderBy('shipping_date', SortOrder::ASC->value)
            ->chunk(100, function ($shipts) use ($bar, $repo, $service){
                foreach($shipts as $s) {
                    $items = ShippingLog::query()->where('order_id', $s->order_id)->with(
                                                                                    ['stockpile', 'stockpile.cardinfo'])->get();
                    $platform = ShopPlatform::determine($s->order_id);
                    $orders = $repo->findByOrderId($platform, $s->order_id);
                    if (!isset($orders)) {
                        $this->info("注文番号({$s->order_id})の登録を開始します。");
                        $this->info("注文番号({$s->order_id})の商品数: {$items->count()}");
                        $subtotal = $items->sum('total_price');
                        $this->info("注文番号({$s->order_id})の合計金額: ¥{$subtotal}");

                        [$feeId, $feeMethod, $feePrice] = $service->determine($platform, $items, $subtotal);
                        $this->info("注文番号({$s->order_id})の発送日：$s->shipping_date");
                        $this->info("注文番号({$s->order_id})の送料：{$feeMethod->value}(¥$feePrice)");

                        $grandTotal = $subtotal + $feePrice;
                        $row = [
                            SC::PLATFORM => $platform,
                            SC::ORDER_ID => $s->order_id,
                            SC::BUYER => $s->name,
                            SC::ZIPCODE => $this->isRakuraku($feeMethod) ? '100-0000' : $s->zip_code,
                            SC::ADDRESS => $this->isRakuraku($feeMethod) ? '匿名希望' : $s->address,
                            SC::SHIPT_DATE => $s->shipping_date,
                            SC::ITEM_SUBTOTAL => $grandTotal,
                            SC::GRAND_TOTAL => $grandTotal,
                            SC::DISCOUNT_AMOUNT => 0,
                            SC::FEE => [GCon::ID => $feeId],
                            SC::ITEM_COUNT => $items->count()
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
                    $this->info("注文番号({$s->order_id})、在庫ID({$s->stock_id})の登録が完了しました。");
                    $repo->createOrderItem($orders->id, $item);
                    $bar->advance();
                    }
            });
            $bar->finish();
            $this->newLine(2);
            $ordersCount = Orders::query()->count();
            $this->info("✅ 登録成功件数: {$ordersCount}");
            return self::SUCCESS;
        } catch (NFShiptMethodException $e) {
            $this->error($e->getDetail());
            $bar->finish();
            return self::FAILURE;
        }
    }

    private function isRakuraku(ShiptMethod $method): bool
    {
        return $method === ShiptMethod::RAKURAKU;

    }
}
