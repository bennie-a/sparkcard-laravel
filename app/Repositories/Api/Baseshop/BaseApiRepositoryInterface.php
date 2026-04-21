<?php
namespace App\Repositories\Api\Baseshop;
/**
 * BASE API接続に関するインターフェース
 */
interface BaseApiRepositoryInterface
{
    /**
     * エンドポイント'/1/oauth/token'から
     * アクセストークンを取得する。
     *
     * @param string $code
     * @return array
     */
    public function getAccessToken(string $code):array;

    public function registToken(array $tokens);
    }
