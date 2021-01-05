<?php

namespace App\Http\Controllers\Password;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class CreatePasswordController extends Controller
{
    /**
     * @todo Add docs.
     */
    protected $client;
    protected $endpoint;

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

        $this->endpoint = 'api/v1/customers/password/create';

    }

    /**
     * @todo Add docs.
     */
    public function showForm(Request $request)
    {
        if(empty($request->query('email'))){
            return redirect('/');
        }

        $domain = preg_match("/(?:uat-sweetbonus)/", URL::current()) ? 'uat-sweetbonus' : 'sweetbonus';
        
        return view('passwords.create')->with([
                'email'  =>  $request->query('email'),
                'domain' =>  $domain
            ]);
    }
    
    public function create(Request $request)
    {
        $data = [
            'email'                 => $request->input('email'),
            'password'              => $request->input('password'),
            'password_confirmation' => $request->input('password_confirmation')
        ];

        $response = $this->client->request('POST', $this->endpoint, [
            'headers' => [
                'Content-Type' => 'application/json',
                'cache-control' => 'no-cache',
                'accept' => 'application/json',
            ],
            'json' => $data,
        ]);

        $data = \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
        
        if (!$data['success']) {
            return redirect('/password/create')->with('alert', [
                'type'    => 'warning',
                'message' => 'Os dados não conferem!',
            ]);
        }

        return redirect('/login')->with('alert', [
            'type'    => 'success',
            'message' => 'Senha criada com sucesso!',
        ]);

    }
}
