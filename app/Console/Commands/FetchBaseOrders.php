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
        try {
            $code = 'cd3f6fb94715e5a07abdfa0a6e59d936';
            $this->info(json_encode([
            'grant_type' => 'authorization_code',
            'client_id' => config('baseapi.client_id'),
            'client_secret' => config('baseapi.secret'),
            'code' => $code,
            'redirect_uri' => config('baseapi.api_url'),
        ], JSON_PRETTY_PRINT));

            $token = $service->getAccessToken($code);
        //  $orders = $service->fetchOrders(['start_ordered' => '2026-01-01', 'end_ordered' => '2026-01-31', 'limit' => 10]);
            $this->info(json_encode($token, JSON_PRETTY_PRINT));
        } catch (\RequestException $e) {
            if ($e->hasResponse()) {
                    $this->error((string) $e->getResponse()->getBody());
            }
        }
    }
}
