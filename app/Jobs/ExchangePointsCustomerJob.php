<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Controllers\Exchange\ApplyMask;

class ExchangePointsCustomerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;

    private $endpoint;

    private $points;    

    private $customerName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $points)
    {
        $this->data             = $data;
        $this->points           = $points;
        $this->endpoint         = 'https://transacional.allin.com.br/api';
        $this->customerName     = explode(" ", $this->data->customer->fullname)[0];

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $token = $this->renewToken();

        $cpf = ApplyMask::handle($this->data->customer->cpf, '###.###.###-##');

        $curl = curl_init('https://transacional.allin.com.br/api/?method=enviar_email&output=json&encode=UTF8&token=' . $token);

        $html = view('emails.customers.exchange-points')->with([
            'data'          => $this->data,
            'points'        => $this->points,
            'customer_name' => $this->customerName,
            'cpf'           => $cpf,            
        ]);

        $json = [
            'nm_envio'        => $this->data->customer->fullname,
            'nm_email'        => $this->data->customer->email,
            'nm_subject'      => 'Recebemos a sua solicitação de troca de pontos!',
            'nm_remetente'    => 'Sweet Bonus',
            'email_remetente' => 'envio@sweetbonusclub.com',
            'nm_reply'        => 'envio@sweetbonusclub.com',
            'dt_envio'        => date('Y-m-d'),
            'hr_envio'        => date('H:i'),
            'html'            => base64_encode($html),
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
