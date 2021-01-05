<?php

namespace App\Services;

use GuzzleHttp\Client;

class HasOffersPixelService
{
    private static function getGuzzleClient()
    {
        return new Client();
    }

    public static function dispacth(string $pixel = '')
    {
        $client   = self::getGuzzleClient();
        
        $response = $client->get($pixel);
        
        $res      = $response->getBody()->getContents();

        if ('success=true;' === $res) 
        {
            return true;
        }
        return false;
    }
}
