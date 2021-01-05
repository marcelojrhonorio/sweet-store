<?php

namespace App\Http\Controllers\MobileApp;

use Log;
use Illuminate\Http\Request;
use App\Traits\SweetStaticApiTrait;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;

class AppIndicationsController extends Controller
{
    use SweetStaticApiTrait;
    
    public function verifyHash(Request $request)
    {
        $customers_id = $request->session()->get('id');  

        try {    

            $response = self::executeSweetApi(
                'GET',
                'api/v1/frontend/app-indication?where[customers_id]='.$customers_id,
                []
            );

            if(isset($response->data[0]) && $response->data[0]->hash) {                
                return $response->data[0]->hash;
            }

            $hash = self::createHashIndication($customers_id);           

            return $hash;
                
        } catch (RequestException $exception) {
            Log::debug($exception->getMessage());
        } catch (ConnectException $exception) {
            Log::debug($exception->getMessage());
        } catch (ClientException $exception) {
            Log::debug($exception->getMessage());
        } catch (BadResponseException $exception) {
            Log::debug($exception->getMessage());
        }
    }  

    private static function createHashIndication($customers_id)
    {
        $hash = str_random(13);

        $data = [
            'hash' => $hash,
            'customers_id' => $customers_id
        ];

        try {    

            $response = self::executeSweetApi(
                'POST',
                'api/v1/frontend/app-indication/',
                $data
            );            

            return $hash;
                
        } catch (RequestException $exception) {
            Log::debug($exception->getMessage());
        } catch (ConnectException $exception) {
            Log::debug($exception->getMessage());
        } catch (ClientException $exception) {
            Log::debug($exception->getMessage());
        } catch (BadResponseException $exception) {
            Log::debug($exception->getMessage());
        }
    }

    public function verifyIndicated(Request $request, $data)
    {
        $customers_id = $request->session()->get('id') ?? null;  
        $app_indicated_by = self::getIndicatedBy($data);        

        try {    

            $response = self::executeSweetApi(
                'POST',
                'api/v1/frontend/app-indication/verify-indicated',
                [
                    'customers_id' => $customers_id,
                    'app_indicated_by' => $app_indicated_by,
                ]
            );

            return redirect(env('URL_DOWNLOAD_APP'));
                
        } catch (RequestException $exception) {
            Log::debug($exception->getMessage());
        } catch (ConnectException $exception) {
            Log::debug($exception->getMessage());
        } catch (ClientException $exception) {
            Log::debug($exception->getMessage());
        } catch (BadResponseException $exception) {
            Log::debug($exception->getMessage());
        }
    }

    private static function getIndicatedBy($hash)
    {
        try {    

            $response = self::executeSweetApi(
                'GET',
                'api/v1/frontend/app-indication?where[hash]='.$hash,
                []
            );

            if(isset($response->data[0])) {                
                return $response->data[0]->customers_id;
            }

            return null;
                
        } catch (RequestException $exception) {
            Log::debug($exception->getMessage());
        } catch (ConnectException $exception) {
            Log::debug($exception->getMessage());
        } catch (ClientException $exception) {
            Log::debug($exception->getMessage());
        } catch (BadResponseException $exception) {
            Log::debug($exception->getMessage());
        }
    }
    
}
