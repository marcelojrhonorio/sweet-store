<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CustomerConfirmationResend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $customer;

    private $endpoint;

    /**
     * Create a new job instance.
     */
    public function __construct($customer)
    {
        $this->customer = $customer;
        $this->endpoint = 'https://transacional.allin.com.br/api';
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $token = $this->renewToken();

        $curl = curl_init('https://transacional.allin.com.br/api/?method=enviar_email&output=json&encode=UTF8&token=' . $token);

        $html = view('emails.customers.created')->with([
            'customer' => $this->customer,
        ]);

        $json = [
            'nm_envio' => $this->customer['fullname'],
            'nm_email' => $this->customer['email'],
            'nm_subject' => explode(" ", $this->customer['fullname'])[0] . ', falta só mais um passo...',
            'nm_remetente' => 'Sweet Bonus',
            'email_remetente' => 'envio@sweetbonusclub.com',
            'nm_reply' => 'envio@sweetbonusclub.com',
            'dt_envio' => date('Y-m-d'),
            'hr_envio' => date('H:i'),
            'html' => base64_encode($html),
        ];

        $json = json_encode($json);

        $curl_post_data = ['dados' => $json];

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $curl_response = curl_exec($curl);
    }

    /**
     * Renew All iN token.
     */
    private function renewToken()
    {
        $client = new Client(['base_uri' => $this->endpoint]);

        $params = [
            'method'   => 'get_token',
            'output'   => 'json',
            'username' => env('ALLIN_USER'),
            'password' => env('ALLIN_PASS'),
        ];

        $query = urldecode(http_build_query($params));

        $response = $client->get('?' . $query);

        $json = json_decode($response->getBody()->getContents());

        return $json->token;
    }
}
