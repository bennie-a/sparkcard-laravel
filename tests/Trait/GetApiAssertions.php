<?php
namespace Tests\Trait;
/**
 * 'GET'メソッドのAPIに関する検証クラス
 */
trait GetApiAssertions 
{
    protected function assert_OK(array $condition) {
        $response = $this->execute($condition);
        $response->assertOk();
        return $response;
    }

    private function execute(array $condition) {
        $response = $this->json('GET', $this->getEndPoint(), $condition);
        return $response;
    }
}
