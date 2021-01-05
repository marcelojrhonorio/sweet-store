<?php

namespace App\Traits;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

trait SiiIntegrationTrait
{
    private function getSsiGuzzleClient(): \GuzzleHttp\Client
    {
        return new Client([
            'base_uri' => env('SSI_BASE_URI'),
            'auth'     => [env('SSI_USERNAME'),  env('SSI_PASSWORD')],
            'debug'   => false,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            ]);
        }
        
        private function getArraySsiParams($ssi_lead = null,$type = 1)
        {
            //
            if (null === $ssi_lead || empty($ssi_lead)) {
                return [];
            }
            if($type){
                return [
                    "sourceId" => "694_10200",
                    "country" => "BR",
                    "language" => "pt",
                    "features" => array("daydob", "monthdob", "yeardob", "gender", "postalcode"),
                    "respondent" => [
                        "respondentID" => $ssi_lead->id,
                        "values" => array($ssi_lead->daydob, $ssi_lead->monthdob, $ssi_lead->yeardob, $ssi_lead->gender->id, $ssi_lead->postalcode),
                    ],
                ];
            }
            return [
                    "sourceId" => "694_10200",
                    "country" => "BR",
                    "language" => "pt",
                    "features" => array("daydob", "monthdob", "yeardob", "gender", "postalcode"),
                    "respondents" => [
                        array("respondentID" => $ssi_lead->id,
                        "values" => array($ssi_lead->daydob, $ssi_lead->monthdob, $ssi_lead->yeardob, $ssi_lead->gender->id, $ssi_lead->postalcode)),
                    ],
                ];
        }
        
        private function ssiRequest(Client $client = null, string $method = '', string $uri = '', array $params = [])
        {
            $client ?? $client = $this->getSsiGuzzleClient();
            
            return $client->request($method, $uri, [
                \GuzzleHttp\RequestOptions::JSON=>$params,
                ]);
                
            }
        }
        