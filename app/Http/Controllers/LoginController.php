<?php
/**
 * @todo Add docs.
 */

namespace App\Http\Controllers;

use Browser;
use Session;
use Carbon\Carbon;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use App\Traits\SweetStaticApiTrait;
use App\Events\CustomerVerifiedEvent;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;

/**
 * @todo Add docs.
 */
class LoginController extends Controller
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
    protected function setCurrentUser($customer)
    {
        session([
            'id'                => $customer->id,
            'name'              => isset($customer->name) ? $customer->name : $customer->fullname,
            'email'             => $customer->email,
            'birthdate'         => $customer->birthdate,
            'gender'            => $customer->gender,
            'cep'               => $customer->cep,
            'avatar'            => ($customer->avatar) ? $customer->avatar : null,
            'points'            => $customer->points,
            'token'             => $customer->token,
            'confirmed'         => $customer->confirmed,
            'receive_offers'    => $customer->receive_offers,
            'indicated_by'      => $customer->indicated_by,
            'clicks_share_mail' => $customer->clicks_share_mail,
            'state'             => $customer->state,
            'updated_personal_info_at' => $customer->updated_personal_info_at,
            'ddd' => $customer->ddd,
            'phone_number' => $customer->phone_number,
        ]);
    }

    /**
     * @todo Add docs.
     */
    public function index(Request $request)
    {
        $email    = urldecode($request->query('email', false));
        $password = urldecode($request->query('password', false));

        if ($email) {
            $endpoint = 'api/v1/customers/login';

            $options = [
                'json' => [
                    'email'    => $email,
                    'password' => env('CHANGE_PASS') ? $password : 'sweetpass',
                ],
            ];

            $response = $this->client->post($endpoint, $options);
            $body     = $response->getBody()->getContents();
            $content  = \GuzzleHttp\json_decode($body);

            if ($content->success) {
                $request->session()->put('id', $content->data->id);
                $request->session()->put('name', $content->data->name);
                $request->session()->put('email', $content->data->email);
                $request->session()->put('birthdate', $content->data->birthdate);
                $request->session()->put('gender', $content->data->gender);
                $request->session()->put('cep', $content->data->cep);
                $request->session()->put('avatar', $content->data->avatar);
                $request->session()->put('points', $content->data->points ?? 0);
                $request->session()->put('token', $content->data->token);
                $request->session()->put('confirmed', $content->data->confirmed);
                $request->session()->put('receive_offers', $content->data->receive_offers);
                $request->session()->put('indicated_by', $content->data->indicated_by);              

                return redirect('/');
            }
        }

        $domain = preg_match("/(?:uat-sweetbonus)/", URL::current()) ? 'uat-sweetbonus' : 'sweetbonus';

        if((783212 == session('id')) || (1187504 == session('id')) || (533419 == session('id')) || (69257 == session('id'))) {
            return view('login')->with([
                'domain'    => $domain,
                'smartlook' => true
            ]);
        }

        return view('login')->with([
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

    public function loginShare(Request $request)
    {
        $email = $request->query('email');
        $pass = $request->query('password');

        $request->validate([
            'email'    => 'bail|required|email',
            'password' => 'required',
        ]);
        
        $endpoint = 'api/v1/customers/login';
        $options  = ['json' => $request->only(['email', 'password'])];
        $response = $this->client->post($endpoint, $options);
    
        $body    = $response->getBody()->getContents();
        $content = \GuzzleHttp\json_decode($body);

        if (false === $content->success) {
            return response()->json([
                'success' => false,
                'message' => 'Senha inválida.',
                'data' => [],
            ]); 
        }

        $this->setCurrentUser($content->data);

        return response()->json([
            'success' => true,
            'message' => 'Login efetuado com sucesso.',
            'data' => $content,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'bail|required|email',
            'password' => 'required',
        ]);
        
        $endpoint = 'api/v1/customers/login';
        $options  = ['json' => $request->only(['email', 'password'])];
        $response = $this->client->post($endpoint, $options);

        $body    = $response->getBody()->getContents();
        $content = \GuzzleHttp\json_decode($body);

        if (false === $content->success) {
            return response()->json([
                'success' => false,
                'message' => 'Senha inválida.',
                'data' => [],
            ]); 
        }

        $this->setCurrentUser($content->data);
        self::createCustomerDevice($content->data->id);

        return response()->json([
            'success' => true,
            'message' => 'Login efetuado com sucesso.',
            'data' => $content,
        ]);
    }

    private static function createCustomerDevice(int $customerId) {
        $default = 'unknown';

        $customerDevice = [
            'customers_id'    => $customerId,
            'browser_name'    => Browser::browserName()     ?? $default,
            'browser_family'  => Browser::browserFamily()   ?? $default,
            'platform_name'   => Browser::platformName()    ?? $default,
            'platform_family' => Browser::platformFamily()  ?? $default,
            'device_family'   => Browser::deviceFamily()    ?? $default,
            'device_model'    => Browser::deviceModel()     ?? $default,
        ];

        try {
            $response = self::executeSweetApi(
                'POST',
                '/api/customer-device/v1/frontend/customer-device',
                $customerDevice
            );

            return $response; 

        } catch (RequestException $exception) {
            //Log::debug($exception->getMessage());
        } catch (ConnectException $exception) {
            //Log::debug($exception->getMessage());
        } catch (ClientException $exception) {
            //Log::debug($exception->getMessage());
        }
    }    
    
    /**
     * @todo Add docs.
     */
    public function verifyEmail(Request $request)
    {
        $email = $request->query('email');
        $data = self::executeSweetApi(
            'GET',
            "/api/v1/frontend/customers/find-email",
            ['email' => $email]
        );
        
        if (0 === $data->result) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum usuário encontrado com este e-mail.',
                'data' => [],
            ]);      
        }

        return response()->json([
            'success' => true,
            'message' => 'Usuário encontrado.',
            'data' => $data,
        ]);

    }

    /**
     * @todo Add docs.
     */
    public function logout(Request $request)
    {
        $data = [
            'email' => $request->session()->get('email'),
            'token' => $request->session()->get('token'),
        ];

        $endpoint = 'api/v1/customers/logout';
        $options  = ['json' => $data];

        $response = $this->client->post($endpoint, $options);

        $body    = $response->getBody()->getContents();
        $content = \GuzzleHttp\json_decode($body);

        if (false === $content->success) {
            return redirect('/')->with('alert', [
                'type' => 'danger',
                'message' => 'Erro ao encerrar a sessão.',
            ]);
        }

        $request->session()->flush();

        return redirect('/login')->with('alert', [
            'type' => 'success',
            'message' => 'Sessão encerrada com sucesso.',
        ]);
    }

    public function downloadFile(Request $request)
    {
        $ebook = [
            'emagrecimento'        => storage_path() . '/download/Sweet_10_dicas_para_emagrecer_de_forma_sustentavel.pdf',
            'dinheiro-na-internet' => storage_path() . '/download/Sweet_ganhe_dinheiro_pela_internet_agora.pdf',
            'revenda'              => storage_path() . '/download/Sweet_5_passos_para_se_tornar_uma_revendedora.pdf',
        ];

        $file = $request->query('file');

        return response()->download($ebook[$file]);
    }

    private function getFile($siteOrigin)
    {
        $siteOrigin = explode('/', $siteOrigin);
        $siteOrigin = $siteOrigin[3] ?? 'default';

        $targets = [
            'emagrecimento',
            'dinheiro-na-internet',
            'revenda',
        ];

        return in_array($siteOrigin, $targets) ? $siteOrigin : null;
    }

    public function verify(Request $request, $token)
    {
        $endpoint = 'api/v1/customers/login/verify/' . $token;
        $response = $this->client->get($endpoint);
        $body     = $response->getBody()->getContents();
        $content  = \GuzzleHttp\json_decode($body);

        if (false === $content->success) {
            return redirect('/')->with('alert', [
                'type'    => 'danger',
                'message' => 'Invalid confirmation code.',
            ]);
        }

        $customer = $content->data;
        
        if (!$customer->confirmed) {
            self::doCheckin($customer->id);

            $verified = new CustomerVerifiedEvent($customer);
            event($verified);
        }

        $customer->confirmed = 1;
        $this->setCurrentUser($customer);

        $file = $this->getFile($customer->site_origin);

        if ($file) {
            $downloadUrl = '/download?file=' . $file;

            Session::flash('download.ebook.after.redirect', $downloadUrl);
        }

        return redirect('/');
    }

    private static function doCheckin (int $id)
    {
        $customer = Customer::find($id);

        $customer->confirmed = 1;
        $customer->points += 30;
        $customer->confirmed_at = Carbon::now()->toDateTimeString();
        $customer->save();

        session(['points' => $customer->points]);        
    }    

    public function createLoginPoints(Request $request)
    {
        $customers_id = $request->input('customers_id');

        try {
            $response = self::executeSweetApi(
                'POST',
                '/api/v1/frontend/customer-login-points/verify',
                [
                    'customers_id' => $customers_id
                ]
            );

            if($response->success) {
                session()->put('points', $response->data);

                return response()->json([
                    'success' => true,
                    'data'  => $response->data,
                ]);
            }

            return response()->json([
                'success' => false,
                'data'  => $response,
            ]);

        } catch (RequestException $exception) {
            //Log::debug($exception->getMessage());
        } catch (ConnectException $exception) {
            //Log::debug($exception->getMessage());
        } catch (ClientException $exception) {
            //Log::debug($exception->getMessage());
        }
    }
}
