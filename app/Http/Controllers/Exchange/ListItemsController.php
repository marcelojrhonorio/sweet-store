<?php
/**
 * @todo Add docs.
 */

namespace App\Http\Controllers\Exchange;

use Carbon\Carbon;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Traits\SweetStaticApiTrait;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;

/**
 * @todo Add docs.
 */
class ListItemsController extends Controller
{
    use SweetStaticApiTrait;
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
    public function listByCategory(Request $request, $categoryId)
    {
        $customerId = $request->session()->get('id');

        $endpoint = 'api/v1/products/categories/' . $categoryId . '/products/customer/' . $customerId;
        $response = $this->client->get($endpoint);
        $body     = $response->getBody()->getContents();
        $data     = \GuzzleHttp\json_decode($body); 

        if((783212 == session('id')) || (1187504 == session('id')) || (533419 == session('id')) || (69257 == session('id'))) {
            return view('exchange', [
                'category' => $data->category,
                'products' => $data,
                'smartlook' => true
            ]);
        }

        return view('exchange', [
            'category' => $data->category,
            'products' => $data
            //'smartlook' => self::verifySmartlook()
        ]);
    }
    
    /**
     * @todo Add docs.
     */
    public function listByPriceRange(Request $request, $priceMin = '1', $priceMax = '0')
    {
        $customerId = $request->session()->get('id');

        $endpoint = 'api/v1/products/min/' . $priceMin . '/max/' . $priceMax . '/customer/' . $customerId;
        $response = $this->client->get($endpoint);
        $body     = $response->getBody()->getContents();
        $data     = \GuzzleHttp\json_decode($body);  
        
        if((705241 == session('id')) || (173588 == session('id')) || (3694 == session('id'))) {
            return view('exchange-by-price', [
                'products' => $data,
                'smartlook' => true
            ]);
        }

        return view('exchange-by-price', [
               'products' => $data,
               //'smartlook' => self::verifySmartlook()
        ]);
    }

    private static function verifySmartlook(){

        try {

            $response = self::executeSweetApi(
                'GET',
                '/api/v1/frontend/services-enabled-time/smartlook',
                []
            );
            
            return $response->data;

        } catch (ClientException $e) {
            $content = [];
            
            Log::debug("Client expection, request ->".Psr7\str($e->getRequest()));
            if ($e->hasResponse()) {
                Log::debug("Client expection, response ->".Psr7\str($e->getResponse()));
            }
            
            preg_match('/{.*}/i', $e->getMessage(), $content);

            Log::debug(print_r($content, true));        
                          
        }
        catch (RequestException $e) 
        {            
            Log::debug("Request Expection , request ->".Psr7\str($e->getRequest()));
            if ($e->hasResponse()) {
                Log::debug("Request Expection ->".Psr7\str($e->getResponse()));
            }
        } 
        catch (ConnectException $e) 
        {
            Log::debug("Connection expection, request ->".Psr7\str($e->getRequest()));
            if ($e->hasResponse()) {
                Log::debug("Connection expection, response ->".Psr7\str($e->getResponse()));
            }
        } 

        catch (BadResponseException $e) 
        {
            Log::debug("Bad Response, request ->".Psr7\str($e->getRequest()));
            if ($e->hasResponse()) {
                Log::debug("Bad Response, response ->".Psr7\str($e->getResponse()));
            }
        } 
    }    
}
