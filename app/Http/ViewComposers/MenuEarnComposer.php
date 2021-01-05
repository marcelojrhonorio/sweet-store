<?php

namespace App\Http\ViewComposers;

use GuzzleHttp\Client;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class MenuEarnComposer
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
        $response   = $this->client->get('api/v1/frontend/actions/categories');
        $content    = $response->getBody()->getContents();
        $decoded    = \GuzzleHttp\json_decode($content);
        $categories = $decoded->data;       

        $icons = [
            'images/menu-icon-stamp.png',
            'images/menu-icon-completarseuperfil.png',
            'images/menu-icon-compartilhar.png',
            'images/menu-icon-participardequiz.png',
            'images/menu-icon-responderapesquisas.png',
            //'images/menu-icon-podio.png',
            'images/menu-icon-social-network.png',
            'images/menu-icon-outros1.png',
        ];

        $new = [
            null,
            null,
            null,
            null,
            null,
            //null,
            null,
            null,
        ];

        for ($i = 0; $i <= 6; $i++) {
            $categories[$i]->icon = $icons[$i];
            $categories[$i]->news = $new[$i];
        }

        if(!env('STAMPS_VIEW')){
            unset($categories[0]);            
        }

        $view->with('categories', $categories);
    }
}