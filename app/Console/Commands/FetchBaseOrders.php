<?php

namespace App\Console\Commands;

use App\Services\BaseApi\BaseOrderService;
use Illuminate\Console\Command;

class FetchBaseOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-base-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch orders from BASE API';

    /**
     * Execute the console command.
     * ※認可コードは期限が1時間のため、次のURLから取得すること。
     * https://api.thebase.in/1/oauth/authorize?response_type=code&client_id=e344b1c2d02a1e9b2930cb81e9ca36b4&redirect_uri=https://sparkcard.vercel.app/&scope=read_orders
     */
    public function handle(BaseOrderService $service)
    {
        $token = $service->getAccessToken('89a8a936d039aecbb12a49a11bb405d3');
    //  $orders = $service->fetchOrders(['start_ordered' => '2026-01-01', 'end_ordered' => '2026-01-31', 'limit' => 10]);
        $this->info(json_encode($token, JSON_PRETTY_PRINT));
    }
}
