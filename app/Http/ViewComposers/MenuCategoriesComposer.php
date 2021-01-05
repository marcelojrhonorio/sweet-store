<?php
/**
 * @todo Add docs.
 */

namespace App\Http\ViewComposers;

use GuzzleHttp\Client;
use Illuminate\View\View;

/**
 * @todo Add docs.
 */
class MenuCategoriesComposer
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

    /**
     * @todo Add docs.
     */
    public function compose(View $view)
    {
        $response   = $this->client->get('api/v1/products/categories');
        $body       = $response->getBody()->getContents();
        $categories = \GuzzleHttp\json_decode($body);

        $icons = [
            'images/menu-icon-produtosdelimpeza.png',
            'images/menu-icon-cosmeticos.png',
            'images/menu-icon-alimentos.png',
            'images/menu-icon-eletronicos.png',
            'images/menu-icon-outros2.png',
            'images/menu-icon-social-network.png',
        ];

        $new = [
            null,
            null,
            null,
            null,
            null,
            true,
        ];

        for ($i = 0; $i <= 5; $i++) {
            $categories[$i]->icon = $icons[$i];
            $categories[$i]->news = $new[$i];
        }        

        $view->with('categories', $categories);
    }
}
