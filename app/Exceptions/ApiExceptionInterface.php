<?php
// app/Exceptions/ApiExceptionInterface.php

namespace App\Exceptions;

use Throwable;

interface ApiExceptionInterface extends Throwable
{
    public function getTitle(): string;      // エラータイトル
    public function getStatusCode(): int;    // HTTPステータスコード
    public function getDetail(): string;     // 詳細なエラー説明
}