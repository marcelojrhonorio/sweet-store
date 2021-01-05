<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Traits\SweetStaticApiTrait;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;

class CheckinController extends Controller
{
    use SweetStaticApiTrait;

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
        $endpoint = 'api/v1/checkin';

        $data = [
            'customer_id' => $request->session()->get('id'),
            'action_id'   => $request->input('action_id'),
        ];

        $indicated_by = $request->session()->get('indicated_by');
       
        $mgmAction = self::verifyMGMAction($data, $indicated_by);

        if($mgmAction && (null === $mgmAction[0]->won_points)) {
            self::updateWonPoints($mgmAction);
        }

        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . session()->get('token'),
            ],
            'json' => $data,
        ];

        $response = $this->client->post($endpoint, $options);
        $decoded  = \GuzzleHttp\json_decode($response->getBody()->getContents());

        if(property_exists($decoded,'data')){
            $request->session()->put('points', $decoded->data->points);
        }

        return response()->json($decoded);
    }

    private static function updateWonPoints($mgmAction)
    {
        $id = $mgmAction[0]->id;
        $customers_id = $mgmAction[0]->customers_id;
        $indicated_by = $mgmAction[0]->indicated_by->indicated_by;
        $action_type = $mgmAction[0]->action_type;
        $action_id = $mgmAction[0]->action_id;       
        
        if(session('confirmed') == 1 && (null != session('updated_personal_info_at')))
        {
            //atribui a pontuação ao usuário que compartilhou
            self::updateCustomerPoints($indicated_by, 5);  

            try {

                $response = self::executeSweetApi(
                    'PUT',
                    '/api/share-action/v1/frontend/share-action/'.$id,
                    [
                        'customers_id'  => $customers_id,
                        'indicated_by'  => $indicated_by,
                        'action_type'   => $action_type,
                        'action_id'     => $action_id,
                        'won_points'    => Carbon::now()->toDateTimeString(),
                    ]
                );  

                return $response;            

            } catch (RequestException $exception) {
                Log::debug($exception->getMessage());
            } catch (ConnectException $exception) {
                Log::debug($exception->getMessage());
            } catch (ClientException $exception) {
                Log::debug($exception->getMessage());
            } catch (BadResponseException $exception) {
                Log::debug($exception->getMessage());
            }
        }
        
    }

    private static function updateCustomerPoints(int $indicated_by, int $points)
    {
        try {
            $response = self::executeSweetApi(
                'PUT',
                '/api/v1/frontend/customers/update-customer/points?indicated_by='.$indicated_by.'&points='.$points,
                []
            );  

            return $response;            

        } catch (RequestException $exception) {
            Log::debug($exception->getMessage());
        } catch (ConnectException $exception) {
            Log::debug($exception->getMessage());
        } catch (ClientException $exception) {
            Log::debug($exception->getMessage());
        } catch (BadResponseException $exception) {
            Log::debug($exception->getMessage());
        }
    }

    private static function verifyMGMAction($data, $indicated_by)
    {
        $customers_id = $data['customer_id'];
        $action_id = $data['action_id']; 

        try {            
            $response = self::executeSweetApi(
                'GET',
                '/api/share-action/v1/frontend/share-action?where[customers_id]='.$customers_id.
                                                          '&where[indicated_by]='.$indicated_by.
                                                          '&where[action_id]='.$action_id,
                []
            );
           
            return $response->data;
                
        } catch (RequestException $exception) {
            Log::debug($exception->getMessage());
        } catch (ConnectException $exception) {
            Log::debug($exception->getMessage());
        } catch (ClientException $exception) {
            Log::debug($exception->getMessage());
        } catch (BadResponseException $exception) {
            Log::debug($exception->getMessage());
        }

    }
}
