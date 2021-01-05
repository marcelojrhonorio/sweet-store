<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Traits\SweetStaticApiTrait;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;

use Illuminate\Support\Facades\Session;

class ResearchesSsiStoreController extends Controller
{
    use SweetStaticApiTrait;
    //
    public function index(Request $request)
    {
        if((783212 == session('id')) || (1187504 == session('id')) || (533419 == session('id')) || (69257 == session('id'))) {
            return view('researches',[
                'surveys'=>$this->getResearches(),
                'smartlook' => true
            ]);
        }

        return view('researches',[
                    'surveys'=>$this->getResearches()
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

    private function getResearches(){
        $surveys = self::executeSweetApi(
            'GET',
            env('APP_SWEET_API') . "/api/customers-invites/v1/frontend/invites?limit=100&where[customer_id]=".Session::get('id')."&order=created_at,desc",
            []
        );
        if (empty($surveys) && !property_exists($surveys, 'data')) {
            return array();
        }
        if (empty($surveys->data)) {
            return array();
        }
        return $surveys->data;
    }

}
