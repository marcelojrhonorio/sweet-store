<?php

namespace App\Http\Controllers\MobileApp;

use Log;
use Illuminate\Http\Request;
use App\Traits\SweetStaticApiTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;

class AppMessagePostbackController extends Controller
{
    use SweetStaticApiTrait;

    public function getMessage($message_id)
    {
        try {    

            $response = self::executeSweetApi(
                'GET',
                '/api/app-message/v1/frontend/get-message/' . $message_id,
                []
            );

            if($response->real_link) {
                return redirect($response->real_link);
            }
           
            return redirect($response->link);
                
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
