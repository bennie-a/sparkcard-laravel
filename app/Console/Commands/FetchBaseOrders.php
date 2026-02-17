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
            $code = 'a8e60c265cbf35ffe99896e63b0716c9';
            $token = $service->getAccessToken($code);
            $accessToken = $token['access_token'];
            $this->info('Access Token: ' . $accessToken);
            $this->info('Refresh Token: ' . $token['refresh_token']);
            $orders = $service->fetchOrders($accessToken, ['start_ordered' => '2026-02-01', 'end_ordered' => '2026-02-17', 'limit' => 10]);
            $this->info('Fetched Orders: ' . json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } catch (\RequestException $e) {
            if ($e->hasResponse()) {
                    $this->error((string) $e->getResponse()->getBody());
            }
        }
    }
}
