<?php

namespace App\Http\Controllers\Exchange;

use GuzzleHttp\Client;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Notifications\ExchangePoints;
use App\Jobs\AstrocentroVoucherJob;
use App\Traits\SweetStaticApiTrait;
use App\Jobs\ExchangePointsInternalJob;
use App\Jobs\ExchangePointsCustomerJob;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Exchange\ApplyMask;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;

class CheckoutController extends Controller
{
    use SweetStaticApiTrait;

    protected $client;

    private $endpoint = '';

    public function __construct()
    {
        $base = env('APP_SWEET_API') . '/api/exchange/v1/frontend';

        $this->endpoint = $base . '/exchanged-points';

        $this->client = new Client();

    }

    public function checkout(Request $request) {

        $data = [
            'status_id'           =>  1,
            'customers_id'        =>  $request->input('customer_id'),
            'points'              =>  $request->input('item_points'),
            'product_services_id' =>  $request->input('item_id'),
            'cep'                 =>  $request->input('cep'),
            'address'             =>  $request->input('address'),
            'number'              =>  $request->input('number'),
            'reference_point'     =>  $request->input('reference'),
            'neighborhood'        =>  $request->input('neighborhood'),
            'city'                =>  $request->input('city'),
            'complement'          =>  $request->input('complement'),
            'state'               =>  $request->input('state'),
        ];

        $customerData = [
            'customer_id'    =>  $request->input('customer_id'),
            'cpf'            =>  $request->input('cpf'),
            'phone'          =>  $request->input('phone'),
            'item_points'    =>  $request->input('item_points'),
            'update_address' =>  $request->input('update_address'),
            'cep'            =>  $request->input('cep'),
            'city'           =>  $request->input('city'),
            'state'          =>  $request->input('state'),            
        ];

        $validator = Validator::make($request->only('phone'), [
            'phone' => 'required|string|size:14',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => 'O campo telefone é inválido',
                'data'    => [],
            ]);
        }

        $validator = Validator::make($request->only('cpf'), [
            'cpf' => 'required|cpf',
        ]);
       
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => 'O campo cpf é inválido',
                'data'    => [],
            ]);
        }

        /**
         * verify if cpf is duplicated
         */
        $customer = Customer::find($customerData['customer_id']);

        $cpfCondition1 = sizeof(Customer::where('cpf', $customerData['cpf'])->get()) > 1;
        $cpfCondition2 = (sizeof(Customer::where('cpf', $customerData['cpf'])->get()) == 1) && $customer->cpf != $customerData['cpf'];        

        if ($cpfCondition1 || $cpfCondition2) {
            return response()->json([
                'success' => false,
                'errors'  => 'O cpf já está em uso',
                'data'    => [],
            ]);
        }        

        $validator = Validator::make($request->only('number'), [
            'number'  =>  'required|string',
        ]);

        if ($validator->fails() || '' == $data['number']) {
            return response()->json([
                'success' => false,
                'errors'  => 'O campo número é inválido',
                'data'    => [],
            ]);
        }

        $customerData['cep'] = ApplyMask::handle($data['cep'], '##.###-####');
        $data['cep']         = ApplyMask::handle($data['cep'], '##.###-####');
        
        $points = $this->updateCustomer($customerData, $request);

        $created = $this->createExchangedPoint($data);
        
        $exchange = $this->findExchangedPoint($created->id);
        
        ExchangePointsInternalJob::dispatch($exchange, $points)->onQueue('store_exchange_internal');
        ExchangePointsCustomerJob::dispatch($exchange, $points)->onQueue('store_exchange_customer');
        
        if (preg_match("/(?:Astrocentro)/", $exchange->product_service->title)) {
            $updated = $this->updateExchangeStatus($exchange->id, $data);
            AstrocentroVoucherJob::dispatch($exchange)->onQueue('store_astrocentro_voucher');
        }


        $request->session()->forget('points');
        $request->session()->put('points', $points['final']);

        $pointsAfter = \Session::get('points');

        /**
         * Send Slack Notification
         */
        $customerNotification = Customer::first();
        $customerNotification->notify(new ExchangePoints($exchange));

        return response()->json([
            'success' => true,
            'data'    => [
                'created' => $created,
                'customer_points' => $pointsAfter,
                ],
        ]);

    }

    public function getLastAddress ($customerId) {
        
        $exchanges = $this->getExchangeByCustomerId($customerId);

        if([] === $exchanges) {
            return response()->json([
                'success' => false,
                'data'    => [],
            ]);
        }

        foreach ($exchanges as $e) {
            $lastAddress = [
                'address' => $e->address,
                'number' => $e->number,
                'neighborhood' => $e->neighborhood,
                'city' => $e->city,
                'cep' => $e->cep,
                'complement' => $e->complement,
                'reference_point' => $e->reference_point,
                'state' => $e->state,
            ];
        }

        if('default' == $lastAddress['address']) {
            return response()->json([
                'success' => false,
                'data'    => [],
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => $lastAddress,
        ]);  

    }

    private function updateExchangeStatus ($id, $data) {

        $data['status_id'] = 7;

        $response = $this->client->request('PUT', $this->endpoint . '/' . $id, [
            'form_params' => $data,
        ]);

        $content  = $response->getBody()->getContents();

        $contentJson = \GuzzleHttp\json_decode($content);

        return $contentJson->data;

    }

    private function getExchangeByCustomerId ($id) {
        
        $response = $this->client->request('GET', $this->endpoint . '?where[customers_id]=' . $id);
        
        $content = $response->getBody()->getContents();

        $contentJson = \GuzzleHttp\json_decode($content);

        return $contentJson->data;
    }

    private function findExchangedPoint($id){
        
        $response = $this->client->request('GET', $this->endpoint . '/' . $id);
        
        $content = $response->getBody()->getContents();

        $contentJson = \GuzzleHttp\json_decode($content);

        return $contentJson;

    }

    private function createExchangedPoint($data){

        $response = $this->client->request('POST', $this->endpoint, [
            'form_params' => $data,
        ]);

        $content = $response->getBody()->getContents();

        $contentJson = \GuzzleHttp\json_decode($content);

        return $contentJson->data;
    }

    private function updateCustomer($data, $request)
    {
        $customer = Customer::find($data['customer_id']);

        $points = [
            'previous_ballance' => $customer->points,
            'final'             => '',
        ];

        $ddd = substr($data['phone'], 1, 2);
        $number = substr($data['phone'], 4, 10);
        
        $customer->ddd          = $ddd;
        $customer->phone_number = $number;
        $customer->cpf          = $data['cpf'];
        $customer->points -= $data['item_points'];

        if(1 == $data['update_address']) {
            $customer->state = $data['state'];
            $customer->city  = $data['city'];
            $customer->cep   = $data['cep'];
        }

        $customer->save();

        $points['final'] = $customer->points;

        return $points;
    }

    public function getProductService (Request $request) 
    {
        $response = $this->client->request(
            'GET', 
            env('APP_SWEET_API') . '/api/v1/frontend/products-services/' . $request->input('id')
        );

        $content = $response->getBody()->getContents();

        $contentJson = \GuzzleHttp\json_decode($content);
       
        return response()->json([
            'success' => true,
            'data'    => $contentJson,
        ]);
    }

    public function verifyLastExchange(Request $request)
    {
        $customers_id = $request->input('customer_id');

        try {

            $response = self::executeSweetApi(
                'POST',
                '/api/exchange/v1/frontend/exchanged-points/get-last-exchange',
                [
                    'customers_id' => $customers_id
                ]
            );
        
            return response()->json([
                'success' => true,
                'data'    => $response,
            ]);

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

    public function verifyStampsRequired(Request $request)
    {
        $product_id = $request->input('product_id');
        $customer_id = $request->input('customer_id');
        $customer_points = $request->input('customer_points');
        $item_points = $request->input('item_points');
        
        //selos exigidos para troca do produto
        $stamps = self::getProductServiceStamps($product_id);
        
        //array de selos ainda não conquistados
        $stamps_in_progress = [];

        //selos conquistados
        $selos = [];
        
        /**
         * Para cada selo exigido na troca,
         * verificar se já foi conquistado
         * 
         */
        foreach ($stamps as $stamp) 
        {
            //verificar se selo já foi conquistado
            $status = self::getStatusByStamp($customer_id, $stamp);

            //se ainda não foi conquistado, inserir no array 
            if($status) {
                array_push($selos, $stamp);
            } else {
                array_push($stamps_in_progress, $stamp);
            }
        }

        //tratamento para ordenar os selos: 'conquistados -> não conquistados'
        $conquistados = [];
        foreach($selos as $selo)
        {
            array_push($conquistados, self::getStamp($selo)); 
        }
        
        foreach($stamps_in_progress as $stamp)
        {
            array_push($conquistados, self::getStamp($stamp));         
        }

        $condition = $customer_points >= $item_points;

        //tem os pontos suficientes e tem os selos exigidos
        if((0 == count($stamps_in_progress)) && $condition) {
            return response()->json([
                'success' => true,
                'stamps'  => [],
                'stamps_required'  => $conquistados,
            ]);

        //tem os pontos suficientes, mas não tem os selos exigidos
        } else if ((0 != count($stamps_in_progress)) && $condition) {
            $stamps = [];            
            foreach($stamps_in_progress as $stamp)
            {
                array_push($stamps, self::getStamp($stamp));         
            }
            
            return response()->json([
                'success' => false,
                'title'   => "Ops! Você ainda precisa conquistar alguns selos.",
                'message' => "Você tem a pontuação necessária, mas ainda precisa de algum(ns) selo(s).",
                'stamps'  => $stamps,
                'stamps_required'  => $conquistados,
            ]);

        //não tem os pontos suficientes, mas tem os selos exigidos
        } else if ((0 == count($stamps_in_progress)) && (!$condition) && (0 != count($stamps)))  {
            return response()->json([
                'success' => false,
                'title'   => "Ops! Você ainda não tem a pontuação suficiente.",
                'message' => "Você tem todos os selos necessários, mas ainda precisa acumular a pontuação.",
                'stamps'  => [],
                'stamps_required'  => $conquistados,
            ]);

        //ainda não tem selos para o produto
        } else if ((0 == count($stamps))) {            
            return response()->json([
                'success' => false,
                'title'   => "Ops! Ainda não é possível realizar a troca.",
                'message' => "Você não tem pontos suficiente para esse produto ainda.
                 Sem problemas! Faça mais atividades e ganhe mais pontos!",
                'stamps'  => [],
                'stamps_required'  => [],
            ]);

        //não tem os pontos suficientes e não tem os selos exigidos
        } else {
            $stamps = [];
            foreach($stamps_in_progress as $stamp)
            {
                array_push($stamps, self::getStamp($stamp));         
            }

            return response()->json([
                'success' => false,
                'title'   => "Ops! Ainda não é possível realizar a troca.",
                'message' => "Você precisa acumular a quantidade de pontos necessária e conquistar algum(ns) selo(s):",
                'stamps'  => $stamps,
                'stamps_required'  => $conquistados,
            ]);
        }
    }

    private static function getProductServiceStamps (int $product_id) 
    {
        try {

            $response = self::executeSweetApi(
                'GET',
                '/api/v1/product-service-stamps?where[product_id]='.$product_id,
                []
            );

            $array = [];

            foreach($response->data as $stamp)
            {
                array_push($array, $stamp->stamps_id);
            }
        
            return $array;

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

    private static function getStatusByStamp (int $customer_id, int $stamp_id)
    {         
        $required_amount = self::getRequiredAmount($stamp_id);
        $count_to_stamp = self::getCountToStamp($customer_id, $stamp_id);

        if($required_amount == $count_to_stamp) {
            return true;
        }

        return false;
    }

    private static function getRequiredAmount(int $stamp_id)
    {
        try {

            $response = self::executeSweetApi(
                'GET',
                '/api/stamps/v1/frontend/stamps/'.$stamp_id,
                []
            );

            if($response) {
                return $response->required_amount;
            }

            return;

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

    private static function getCountToStamp(int $customer_id, int $stamp_id)
    {
        try {

            $response = self::executeSweetApi(
                'GET',
                '/api/stamps/v1/frontend/customer-stamps?where[customers_id]='.$customer_id.'&where[stamps_id]='.$stamp_id,
                []
            );
            
            $obj = get_object_vars($response);  

            if($obj['data']) {
                return $obj['data'][0]->count_to_stamp;
            }

            return;

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

    public function getCustomerPoints (Request $request)
    {
        $points = $request->session()->get('points');

        return response()->json([
            'success' => true,
            'data'    => $points,
        ]);
    }

    private static function getStamp(int $id)
    {
        try {

            $response = self::executeSweetApi(
                'GET',
                '/api/stamps/v1/frontend/stamps/'.$id,
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

}