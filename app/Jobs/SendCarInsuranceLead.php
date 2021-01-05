<?php

namespace App\Jobs;

use App\Services\HasOffersPixelService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendCarInsuranceLead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $researchData;

    private $pixels;

       /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;
    /**
     * Create a new job instance.
     */
    public function __construct($researchData)
    {
        $this->researchData = $researchData;
        $this->pixels = [
            'new' => 'http://sweet.go2cloud.org/aff_lsr?offer_id=176&aff_id=1016',
            'renew' => 'http://sweet.go2cloud.org/aff_goal?a=lsr&goal_id=10&aff_id=1016',
            'oldcar' => 'http://sweet.go2cloud.org/aff_goal?a=lsr&goal_id=11&aff_id=1016',
        ];
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Definir lead
        $lead = [];
        $lead['vehicle_model'] = $this->researchData->vehicle_model;
        $lead['postal_code'] = preg_replace('/\D/', '', $this->researchData->postal_code);
        $lead['vehicle_year'] = $this->researchData->vehicle_year;
        $lead['vehicle_manufacturer'] = $this->researchData->vehicle_manufacturer;
        $lead['date_of_birth'] = date('d/m/Y', strtotime($this->researchData->date_of_birth));
        $lead['renewal'] = $this->researchData->isRenew ? 'Sim' : 'Não';
        $lead['sex'] = $this->researchData->sex === 'M' ? 'Masculino' : 'Feminino';
        $lead['name'] = $this->researchData->name;
        $lead['lead_type'] = 'Seguro Auto';
        $lead['vehicle_type'] = 'Carro';
        $lead['email'] = $this->researchData->email;
        $lead['cellphone'] = preg_replace('/\D/', '', $this->researchData->cellphone);
        $lead['telephone'] = preg_replace('/\D/', '', $this->researchData->telephone);
        $lead['national_identity'] = preg_replace('/\D/', '', $this->researchData->national_identity);
        $lead['fipe'] = $this->researchData->fipe;

        // Dados completos
        $data = [];
        $data['api_key'] = env('VEMMLEADS_API_KEY');
        $data['submission'] = $lead;

        $this->httpBuildQueryForCurl($data, $post);

        try {
            $client = new Client();

            $response = $client
                ->request(
                    'POST',
                    env('VEMMLEADS_API_URL'),
                    [
                        'form_params' => $post,
                        'http_errors' => true,
                    ]
                );

            $content = $response->getBody()->getContents();

            $contentJson = \GuzzleHttp\json_decode($content);

            if (empty($contentJson)) {
                return false;
            }
            // persistir status da sicronização com seguro alto
            $this->updateResearch($contentJson->status, $contentJson->code, $this->researchData);

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
        catch (ClientException $e) 
        {
            Log::debug("Client expection, request ->".Psr7\str($e->getRequest()));
            if ($e->hasResponse()) {
                Log::debug("Client expection, response ->".Psr7\str($e->getResponse()));
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

    private function updateResearch($status = null, $code = 0, $research = null)
    {
        $answer = $this->getResearchAnswer($research->research_id);

        $answer->customer_research_answer_status_sicronized = $status;

        if (201 == $status && 0 == $code) {
            $answer->customer_research_answer_status_sicronized = 1;
        }
        // HasOffersPixelService::dispacth($this->pixels[self::getRulePixel($this->researchData->vehicle_age, $this->researchData->isRenew)]);

        $apiClient = new Client();

        $endpoint = env('APP_SWEET_API') . '/api/seguroauto/v1/frontend/customer-research-answers/' . $answer->id;

        $response = $apiClient
            ->request(
                'PUT',
                $endpoint,
                ['form_params' => $answer]
            );

        $content = $response->getBody()->getContents();

        // $dataJson = \GuzzleHttp\json_decode($content)->data;

    }

    private function getResearchAnswer($id = 0)
    {
        $apiClient = new Client();

        $endpoint = env('APP_SWEET_API') . '/api/seguroauto/v1/frontend/customer-research-answers';

        $response = $apiClient->get($endpoint . '?where[customer_research_id]=' . $id);

        $content = $response->getBody()->getContents();

        $dataJson = \GuzzleHttp\json_decode($content)->data[0];

        return $dataJson;

    }
    /**
     * Transformar Array aos parametros
     */
    private function httpBuildQueryForCurl($arrays, &$new = array(), $prefix = null)
    {
        if (is_object($arrays)) {
            $arrays = get_object_vars($arrays);
        }

        foreach ($arrays as $key => $value) {
            $k = isset($prefix) ? $prefix . '[' . $key . ']' : $key;

            if (is_array($value) || is_object($value)) {
                $this->httpBuildQueryForCurl($value, $new, $k);
            } else {
                $new[$k] = $value;
            }
        }
    }
    private static function getRulePixel($age = 0, $renew = false): string
    {
        if (14 < $age) {
            return 'oldcar';
        }
        if ($renew) {
            return 'renew';
        }
        return 'new';
    }
}
