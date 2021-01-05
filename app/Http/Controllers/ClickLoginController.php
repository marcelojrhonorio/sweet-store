<?php
/**
 * @todo Add docs.
 */

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

/**
 * @todo Add docs.
 */
class ClickLoginController extends Controller
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
            'base_uri'    => env('APP_SWEET_API'),
            'http_errors' => false,
            'headers'     => [
                'cache-control' => 'no-cache',
                'accept'        => 'application/json',
                'content-type'  => 'application/json',
            ],
        ]);
    }

    /**
     * @todo Add docs.
     */
    private function setCurrentUser($customer)
    {
        session([
            'id'        => $customer->id,
            'name'      => $customer->fullname,
            'email'     => $customer->email,
            'birthdate' => $customer->birthdate,
            'gender'    => $customer->gender,
            'cep'       => $customer->cep,
            'avatar'    => $customer->avatar,
            'points'    => $customer->points,
            'token'     => $customer->token,
            'confirmed' => $customer->confirmed,
        ]);
    }

    public function login(Request $request)
    {
        $email = $request->input('email');

        $endpoint = 'api/v1/customers/click-login?email=' . $email;

        $response = $this->client->get($endpoint);

        $body = $response->getBody()->getContents();

        $json = \GuzzleHttp\json_decode($body);

        if (false === $json->success) {
            return redirect('/')->with('alert', [
                'type'    => 'danger',
                'message' => 'E-mail inválido ou não encontrado.',
            ]);
        }

        if ('password_must_be_changed' === $json->status) {
            return redirect('/password/change')->with('alert', [
                'type'    => 'danger',
                'message' => 'Por favor, altere sua senha.',
            ]);
        }        

        $customer = $json->data;

        $this->setCurrentUser($customer);

        return redirect('/');
    }
}
