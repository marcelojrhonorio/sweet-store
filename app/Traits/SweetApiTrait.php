<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\json_decode;

trait SweetApiTrait
{
    // getClient
    private function getSweetClient(): \GuzzleHttp\Client
    {
        return new Client([
            'base_uri' => env(' APP_SWEET_API '),
        ]);
    }

    private function executeSweetApi(string $method = '', string $uri = '', array $params = [])
    {
        $client = $this->getSweetClient();

        $client_params = [
            'headers' => [
            ],
        ];

        empty($params) ?? $client_params = array_merge(['json' => $params], $client_params);

        $response = $client->request($method, $uri, [
            $client_params,
        ]);

        return $this->getContentApi($response);
    }

    private function getContentApi($response = null){
        return json_decode($response->getBody()->getContents());
    }
}
