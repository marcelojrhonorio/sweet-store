<?php

namespace App\Http\Controllers\Password;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;

class ResetPasswordController extends Controller
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

    public function showResetForm(Request $request, $token = null)
    {
        $domain = preg_match("/(?:uat-sweetbonus)/", URL::current()) ? 'uat-sweetbonus' : 'sweetbonus';

        return view('passwords.reset')->with([
                'token'   => $token,
                'domain'  => $domain
            ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $endpoint = 'api/v1/customers/password/reset';

        $options = ['json' => $request->all()];

        $response = $this->client->post($endpoint, $options);

        $body    = $response->getBody()->getContents();
        $content = \GuzzleHttp\json_decode($body);

        if (false === $content->success) {
            return redirect('/')->with('alert', [
                'type'    => 'danger',
                'message' => 'Falha ao resetar a senha.',
            ]);
        }

        return redirect('/login')->with('alert', [
            'type'    => 'success',
            'message' => 'Senha alterada com sucesso.',
        ]);
    }
}
