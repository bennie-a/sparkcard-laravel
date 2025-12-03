<?php
// app/Exceptions/ApiException.php

namespace App\Exceptions;

use Exception;
/**
 * APIに関する例外クラス
 */
class ApiException extends Exception implements ApiExceptionInterface
{
    public function getTitle(): string
    {
        return 'Api Error';
    }

    public function getStatusCode(): int
    {
        return 500;
    }

    public function getDetail(): string
    {
        return 'Unexpected error occurred.';
    }

    public function getSpecifics(): array
    {
        return [];
    }
}