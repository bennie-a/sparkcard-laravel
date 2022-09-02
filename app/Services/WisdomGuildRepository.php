<?php
namespace App\Services;
use App\Factory\GuzzleClientFactory;
class WisdomGuildRepository {
    // public function getAll(): Collection;
    public function getAll() {
        $param = [ 
        'query' => [
            'set' => ['DMU'],
            'color' => ['black'],
            'sort'=> 'eidcid'
        ]
    ];
        $client = GuzzleClientFactory::create('wisdom');
        logger()->debug("Client get");
        $response = $client->request("GET", 'search.php', $param);
        return $response->getBody()->getContents();
    }

    public function getCard($url) {
        $client = GuzzleClientFactory::createByUrl($url);
        $response = $client->request('GET', '');
        return $response->getBody()->getContents();
    }
}