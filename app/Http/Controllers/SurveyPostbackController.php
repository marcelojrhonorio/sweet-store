<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class SurveyPostbackController extends Controller
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

    public function store(Request $request)
    {
        $endpoint = 'api/v1/survey';

        $data = [
            'survey_id'     => $request->query('surveyId'),
            'survey_type'   => $request->query('surveyType'),
            'customer_id'   => $request->query('customerId'),
        ];

        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . session()->get('token'),
            ],
            'json' => $data,
        ];
        $response = $this->client->post($endpoint, $options);
        $decoded  = \GuzzleHttp\json_decode($response->getBody()->getContents());
        
        if ($decoded->success){
            return redirect('/');
        }
    }
}
