<?php

namespace App\Exceptions;

use App\Http\Response\CustomResponse;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

use function Symfony\Component\HttpKernel\DataCollector\getMessage;

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
        ConflictException::class,
        ApiException::class
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
     * @param $request
     * @param Throwable $e
     * @return JsonResponse
     */
    public function render($request, Throwable $e): JsonResponse {
        $statusCode = match (true) {
            $e instanceof ValidationException => Response::HTTP_BAD_REQUEST,
            $e instanceof HttpException => $e->getStatusCode(),
            $e instanceof ApiExceptionInterface => $e->getStatusCode(),
            default =>  Response::HTTP_INTERNAL_SERVER_ERROR
        };

        $title = "";
        $detail = "";
        if ($e instanceof ApiExceptionInterface) {
            $title = $e->getTitle();
            $detail = $e->getDetail();
        } else if ($statusCode === Response::HTTP_BAD_REQUEST) {
            $title = "Validation Error";
            $detail = $e->getMessage();
        }

        $json =[
                'title' => $title,
                'status' => $statusCode,
                'detail' => $detail,
                'request' => $request->path()
            ];
        if(!empty($e->getSpecifics())) {
            $json['specifics'] = $e->getSpecifics();
        } 
        logger()->info('エラー：', $json);
        return response()->json($json, $statusCode,  [
            'Content-Type' => 'application/problem+json',
        ]);
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (HttpException  $e, $request) {
            $title = 'Error';
            $detail = '';
            // logger()->debug($e);
            if ($request->is('api/*')) {
                if ($e->getStatusCode() == Response::HTTP_NO_CONTENT) {
                    $detail = '検索結果がありません。';
                } else {
                    $detail = $e->getMessage();
                }

                switch ($e->getStatusCode()) {
                    case Response::HTTP_NO_CONTENT:
                        $title = 'No Contents';
                    break;
                    case Response::HTTP_NOT_FOUND:
                        $title = 'Not Found';
                    case Response::HTTP_CONFLICT:
                        $title = 'Conflict';
                    break;
                    case CustomResponse::HTTP_NOT_FOUND_EXPANSION:
                        $title = 'Not Found Expansion';
                    break;
                    case CustomResponse::HTTP_CSV_VALIDATION:
                        $title = 'CSV Validation Error';
                    break;
                    case CustomResponse::HTTP_NOT_FOUND_CARD:
                        $title = 'Not Found Card in API';
                    break;
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
