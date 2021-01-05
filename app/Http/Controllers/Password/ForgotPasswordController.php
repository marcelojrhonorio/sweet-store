<?php

namespace App\Http\Controllers\Password;

use File;
use Carbon\Carbon;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Jobs\ReceivePasswordJob;
use App\Traits\SweetStaticApiTrait;
use App\Jobs\DeleteFilePasswordJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;

class ForgotPasswordController extends Controller
{
    use SweetStaticApiTrait;

    /**
     * @todo Add docs.
     */
    protected $client;
    protected $endpointUpdate;

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

        $this->endpointUpdate = 'api/v1/customers/password/update';

    }
    /**
     * @todo Add docs.
     */
    public function showReceivePasswordForm()
    {
        $domain = preg_match("/(?:uat-sweetbonus)/", URL::current()) ? 'uat-sweetbonus' : 'sweetbonus';

        if((783212 == session('id')) || (1187504 == session('id')) || (533419 == session('id')) || (69257 == session('id'))) {
            return view('passwords.receive', [
                'domain'    => $domain,
                'smartlook' => true
            ]);
        }

        return view('passwords.receive', [
                    'domain'    => $domain
                    //'smartlook' => self::verifySmartlook()
            ]);
    }    

    /**
     * @todo Add docs.
     */

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

    public function sendNewPasswordEmail(Request $request){
        
        $password = str_random(12);

        $data = [
            'email'    => $request->input('email'),
            'password' => $password
        ];

        $response = $this->client->request('POST', $this->endpointUpdate, [
            'headers' => [
                'Content-Type' => 'application/json',
                'cache-control' => 'no-cache',
                'accept' => 'application/json',
            ],
            'json' => $data,
        ]);

        $data = \GuzzleHttp\json_decode($response->getBody()->getContents(), true);

        if (!$data['success']) {            
            return redirect('/password/receive')->with('alert', [
                'type'    => 'warning',
                'message' => 'Ops, não encontramos o usuário deste e-mail. Não se preocupe. <a href="'.env('SWEETBONUS_URL').'">Cadastre-se</a> agora!',
            ]);
        }
        
        if(!$data['data']['updated']) {
            $password = File::get(storage_path('pass/' . $data['data']['customer']['id'].'.txt'));

        } else {
            File::put(storage_path('pass/' . $data['data']['customer']['id'] . '.txt'), $password);
            DeleteFilePasswordJob::dispatch($data['data']['customer']['id'])
                                    ->onQueue('delete_password_file')
                                    ->delay(now()->addMinutes(5));
        }

        ReceivePasswordJob::dispatch($data, $password)->onQueue('store_receive_password');

        return redirect('/login')->with('alert', [
            'type'    => 'success',
            'message' => 'Senha enviada com sucesso! Cheque sua caixa de entrada.',
        ]);
    }

}
