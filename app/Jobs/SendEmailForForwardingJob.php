<?php

namespace App\Jobs;

use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Traits\SweetStaticApiTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Exception\BadResponseException;

class SendEmailForForwardingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use SweetStaticApiTrait;

    private $name;
    private $email;
    private $customers_id;
    private $endpoint;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($customers_id, $name, $email)
    {
        $this->name = $name;
        $this->email = $email;
        $this->customers_id = $customers_id;
        $this->endpoint = 'https://transacional.allin.com.br/api';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $token = $this->renewToken();

        $curl = curl_init('https://transacional.allin.com.br/api/?method=enviar_email&output=json&encode=UTF8&token=' . $token);      
            
        $html = view('emails.customers.standard-forwarding-email')->with([
            'name' => $this->name,
        ]);

        $json = [
            'nm_envio'        => $this->name,
            'nm_email'        => $this->email,
            'nm_subject'      => $this->name.', você está por dentro da importância do isolamento social?',
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

    private function renewToken ()
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
