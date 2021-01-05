<?php

namespace App\Listeners;

use App\Events\CarInsuranceCreated;
use App\Jobs\SendCarInsuranceLead;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class IsCarInsuranceLead
{
    private $client;

    private $endpoint;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->client = new Client();
        $this->endpoint = env('APP_SWEET_API') . '/api/seguroauto/v1/frontend/veem-leads';
    }

    /**
     * Handle the event.
     *
     * @param  CarInsuranceCreated  $event
     */
    public function handle(CarInsuranceCreated $event)
    {
        // Customer que respondeu a pesquisa.
        $customer_id = $event->customer_id;

        // Endpoint da API para pegar dados da pesquisa.
        $endpoint = $this->endpoint . '?where[customer_id]=' . $customer_id;

        // Pega resposta da API e transforma em JSON
        $response = $this->client->request('GET', $endpoint);

        $content = $response->getBody()->getContents();

        $contentJson = \GuzzleHttp\json_decode($content);

        // Atributos da pesquisa em JSON
        $data = $contentJson->data[0];

        // Pega idade do veículo e checa a renovação do seguro.
        $carAge = $data->vehicle_age;

        $isRenew = $this->isRenew(
            $data->vehicle_date_insurace,
            $data->now_month_year,
            $data->next_month_year
        );

        // Se a idade do veículo for maior que 14 anos, sai do processo.
        if ($carAge > 14) {
            return false;
        }

        // Se idade e renovação não satisfaz os critérios, sai do processo.
        if (14 >= $carAge && false === $isRenew && $data->vehicle_has_insurance) {
            return false;
        }

        $data->isRenew = $isRenew;
        // Blocked because the client stop service with us
         SendCarInsuranceLead::dispatch($data)->onQueue('car_insurance_leads');
    }

    private function isRenew($insuranceDate, $nowDate, $nextDate)
    {
        $insuranceDate = \DateTime::createFromFormat('mY',$insuranceDate);
        $nowDate = \DateTime::createFromFormat('mY',$nowDate);
        $nextDate = \DateTime::createFromFormat('mY',$nextDate);
        return  ($insuranceDate >= $nowDate && $insuranceDate <= $nextDate);
    }
}
