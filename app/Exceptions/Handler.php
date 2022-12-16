<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (HttpException $e, $request) {
            if ($request->is('api/*')) {
                $title = '';
                $detail = '';

                switch ($e->getStatusCode()) {
                    case Response::HTTP_NO_CONTENT:
                        $title = 'No Contents';
                        $detail = '検索結果がありません。';
                    break;
                    case 410:
                        $title = 'No Promotype';
                        $detail = $e->getMessage();
                }
            }
            return response()->json([
                                'title' => $title,
                                'status' => $e->getStatusCode(),
                                'detail' => $detail,
                            ], $e->getStatusCode(), [
                                'Content-Type' => 'application/problem+json',
                            ]);
        });
    }
}
