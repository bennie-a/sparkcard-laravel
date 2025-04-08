<?php
namespace Tests\Trait;

use Illuminate\Http\Response;

/**
 * 'POST'メソッドで実行するAPIに関する検証トレイト
 */
trait PostApiAssertions {

    /**
     * ファイルアップロードが成功した場合のテストケース
     *
     * @param string $endpoint
     * @param string $query
     * @param array $data
     * @return Response
     */
    public function upload_OK(string $query, array $data) {
        $response = $this->upload($query, $data);
        $response->assertStatus(Response::HTTP_CREATED);
        return $response;
    }

    private function upload(string $query, array $data) {
        $header = [
            'headers' => [
                "Content-Type" => "application/json",
            ]
        ];
        $response = $this->post($this->getEndPoint().$query, $data, $header);
        return $response;
    }
}