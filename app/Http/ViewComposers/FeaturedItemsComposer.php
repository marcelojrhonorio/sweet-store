<?php

namespace App\Http\ViewComposers;

use GuzzleHttp\Client;
use Illuminate\View\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class FeaturedItemsComposer
{
    /**
     * @todo Add docs.
     */
    protected $client;

    /**
     * @todo Add docs.
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('APP_SWEET_API'),
            'http_errors' => false,
            'headers'  => [
                'cache-control' => 'no-cache',
                'accept'        => 'application/json',
                'content-type'  => 'application/json',
            ],
        ]);
    }

    public function compose(View $view)
    {
        $customerId = Session::get('id');

        $endpoint   = 'api/v1/customers/' . $customerId . '/products/closest-exchanges';
        $response   = $this->client->get($endpoint);
        $body       = $response->getBody()->getContents();

        $closestExchanges = \GuzzleHttp\json_decode($body);

        $view->with('products', $closestExchanges);
    }
}
