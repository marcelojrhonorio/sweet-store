<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Traits\SweetStaticApiTrait;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Events\CustomerCheckoutEvent;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\Exchange\ApplyMask;
use GuzzleHttp\Exception\BadResponseException;
use App\Models\CustomerAddress\CustomerAddress;

class ProfileController extends Controller
{
    use SweetStaticApiTrait;

    protected $client;

    const ENDPOINT = 'api/v1/customers';
   
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

    public function index()
    {
        $customerId = session('id');

        if((783212 == session('id')) || (1187504 == session('id')) || (533419 == session('id')) || (69257 == session('id'))) {
            return view('profile.index')->with([
                'usr'       => Customer::find($customerId),
                'data'      => self::getCustomerAddress($customerId),
                'interestTypes' => self::getInterestTypes(),
                'customersInterests' => self::getCustomersInterest($customerId),
                'qtdCustomersInterests' => count(self::getCustomersInterest($customerId)),
                'smartlook' => true
            ]);
        }

        return view('profile.index')->with([
            'usr'       => Customer::find($customerId),
            'data'      => self::getCustomerAddress($customerId),
            'interestTypes' => self::getInterestTypes(),
            'customersInterests' => self::getCustomersInterest($customerId),
            'qtdCustomersInterests' => count(self::getCustomersInterest($customerId))
        ]);
    }

    private static function getCustomersInterest($customerId)
    {
        try {
           
            $response = self::executeSweetApi(
                'GET',
                '/api/v1/frontend/interest/customers-interest?where[customers_id]='.$customerId,
                []
            );

            $response = get_object_vars($response);
    
            return $response['data'];
           
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

    private static function getInterestTypes()
    {
        try {
           
            $response = self::executeSweetApi(
                'GET',
                '/api/v1/frontend/interest/interest-types/',
                []
            );

            $response = get_object_vars($response);
    
            return $response['data'];
           
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

    public function updateReceiveOffers(Request $request)
    {
        $customerId = session('id');

        session()->put('receive_offers', true);
        
        try {
           
            $response = self::executeSweetApi(
                'POST',
                '/api/v1/frontend/customers/update-receive-offers/'. $customerId,
                []
            );

            session()->put('points', $response->points);
    
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

    public function addressRegister(Request $request)
    {
        $formCustomer = self::getDataCustomer($request);
        $formAddress  = self::getDataAddress($request);       

        $validator = Validator::make($request->only('phone1'), [
            'phone1' => 'required|string|size:14',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => 'O campo telefone é inválido',
                'data'    => [],
            ]);
        }
        
        $validator = Validator::make($request->only('cpf'), [
            'cpf' => 'required|string|size:11',
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
        $customer = Customer::find($formCustomer['customers_id']);

        $cpfCondition1 = sizeof(Customer::where('cpf', $formCustomer['cpf'])->get()) > 1;
        $cpfCondition2 = (sizeof(Customer::where('cpf', $formCustomer['cpf'])->get()) == 1) && $customer->cpf != $formCustomer['cpf'];  

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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => 'O campo número é inválido',
                'data'    => [],
            ]);
        }

        $formAddress['cep']  = ApplyMask::handle($formAddress['cep'], '##.###-####');
        $formCustomer['cep'] = ApplyMask::handle($formCustomer['cep'], '##.###-####');

        /**
         * verify if customerAddress exists. 
         * If it exists it will give 'update', 
         * otherwise it will create a new one.
         */
        $cAddress = self::getCustomerAddress($formAddress['customers_id']);
        $cAddressResponse = ($cAddress) ? self::updateCustomerAddress($cAddress, $formAddress) : self::createCustomerAddress($formAddress);
       
        $customerResponse = get_object_vars(self::updateCustomerData($customer, $formCustomer));
    
        $indicate = $customer->indicated_by;

        /**
         * This is the check for 'Member Get Member'. 
         * The event will only be called if the user 
         * confirms the email and completes their registration 
         * (verified by the 'firstUpdate' variable, coming from the API).
         */
        if(isset($customerResponse['original']['data']->status))
        {
            if('success' === $customerResponse['original']['data']->status && 
                             $customerResponse['original']['data']->firstUpdate) 
            {
                $checkout = new CustomerCheckoutEvent($indicate, 'member_get_member', '');
                event($checkout);
            }
        }

        return response()->json([
            'success'  => true,
            'data'     => [
                 'customer' => $customerResponse, 
                 'cAddress' => $cAddressResponse,
            ],
        ]);
    }

    private static function getDataCustomer($request)
    {
        if(env('PROFILE_PICTURE'))
        {
            return [
                'customers_id'        =>  (int) $request->input('customer_id'),
                'fullname'            =>  $request->input('fullname'),
                'cep'                 =>  $request->input('cep'),
                'email'               =>  $request->input('email'),
                'birthdate'           =>  $request->input('birthdate'),
                'phone1'              =>  $request->input('phone1'),
                'phone2'              =>  $request->input('phone2') ?? null,
                'cpf'                 =>  $request->input('cpf'),
                'avatar'              =>  $request->input('avatar'),
                'interests'           =>  $request->input('interests'),
            ]; 
        }        

        return [
            'customers_id'        =>  (int) $request->input('customer_id'),
            'fullname'            =>  $request->input('fullname'),
            'cep'                 =>  $request->input('cep'),
            'email'               =>  $request->input('email'),
            'birthdate'           =>  $request->input('birthdate'),
            'phone1'              =>  $request->input('phone1'),
            'phone2'              =>  $request->input('phone2') ?? null,
            'cpf'                 =>  $request->input('cpf'),
            'interests'           =>  $request->input('interests'),
        ];  
    }

    private static function getDataAddress($request)
    {
        return [
            'customers_id'        =>  (int) $request->input('customer_id'),
            'cep'                 =>  $request->input('cep'),
            'street'              =>  $request->input('street'),
            'number'              =>  $request->input('number'),
            'reference_point'     =>  $request->input('reference'),
            'neighborhood'        =>  $request->input('neighborhood'),
            'city'                =>  $request->input('city'),
            'state'               =>  $request->input('state'),
            'complement'          =>  $request->input('complement'),
        ];    
    }

    /**
     * Before updating the user data, 
     * it is checked whether there is any 
     * change in the reported data. 
     * If so, the update is done.
     */
    public static function updateCustomerData($customer, $formCustomer)
    { 
        try {

            if(self::WBMCustomer($customer, $formCustomer))
            {
                $avatar = null;

                if(env('PROFILE_PICTURE'))
                {
                    $avatar = $formCustomer['avatar'];
                }

                $response = self::executeSweetApi(
                    'PUT',
                    '/api/v1/frontend/customers/update-customer-data/'.$formCustomer['customers_id'],
                    [
                        'fullname'       =>  $formCustomer['fullname'],
                        'email'          =>  $formCustomer['email'],
                        'birthdate'      =>  $formCustomer['birthdate'],
                        'phone1'         =>  $formCustomer['phone1'],
                        'phone2'         =>  $formCustomer['phone2'],
                        'cpf'            =>  $formCustomer['cpf'],
                        'cep'            =>  $formCustomer['cep'],
                        'avatar'         =>  $avatar,
                    ]
                );  

                /**
                 * Stamp Profile call
                 */
                $checkout = new CustomerCheckoutEvent($formCustomer['customers_id'], 'profile', 5);
                event($checkout);

                session()->put('avatar', $formCustomer['avatar']);

                return response()->json([
                    'modified' => true,
                    'data'     => $response,    
                ]);
            } 
            
            return response()->json([
                'modified' => true,
                'data'     => [],    
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

    public function createCustomersInterest(Request $request)
    {  
        $customers_id = $request->input('customers_id');
        $interest_types_id = $request->input('interest_types_id');
        $interest = $request->input('interest');

        try {
           
            $response = self::executeSweetApi(
                'POST',
                '/api/v1/frontend/interest/customers-interest/create',
                [
                    'interest_types_id'  =>  $interest_types_id,
                    'customers_id'       =>  $customers_id,
                    'interest'           =>  $interest,
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

    public function deleteCustomersInterest(Request $request)
    {
        $customers_id = $request->input('customers_id');
        $interest_types_id = $request->input('interest_types_id');

        try {
           
            $response = self::executeSweetApi(
                'POST',
                '/api/v1/frontend/interest/customers-interest/delete',
                [
                    'interest_types_id'  =>  $interest_types_id,
                    'customers_id'       =>  $customers_id,
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

    private static function createCustomerAddress($formAddress)
    {   
        try {
           
            $response = self::executeSweetApi(
                'POST',
                '/api/customer-address/v1/frontend/customer-address/',
                [
                    'customers_id'         =>  $formAddress['customers_id'],
                    'cep'                  =>  $formAddress['cep'],
                    'street'               =>  $formAddress['street'],
                    'number'               =>  $formAddress['number'],
                    'reference_point'      =>  $formAddress['reference_point'],
                    'neighborhood'         =>  $formAddress['neighborhood'],
                    'city'                 =>  $formAddress['city'],
                    'state'                =>  $formAddress['state'],
                    'complement'           =>  $formAddress['complement'],
                ]
            );
    
            return response()->json([
                'modified' => true,
                'data'     => $response->data,    
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

    private static function getCustomerAddress(int $id)
    {
        try {
            
            $response = self::executeSweetApi(
                'GET',
                '/api/customer-address/v1/frontend/customer-address?where[customers_id]='. $id,
                []
            );
           
            return $response->data[0] ?? null;

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

    /**
     * Before updating the user address data, 
     * it is checked whether there is any change 
     * in the reported data. If so, the update is done.
     */
    private static function updateCustomerAddress($cAddress, $formAddress)
    {
        try {

            if(self::WBMCustomerAddress($cAddress, $formAddress))
            {                
                $response = self::executeSweetApi(
                    'PUT',
                    '/api/customer-address/v1/frontend/customer-address/'.$cAddress->id,
                    [
                        'customers_id'         =>  $formAddress['customers_id'],
                        'cep'                  =>  $formAddress['cep'],
                        'street'               =>  $formAddress['street'],
                        'number'               =>  $formAddress['number'],
                        'reference_point'      =>  $formAddress['reference_point'],
                        'neighborhood'         =>  $formAddress['neighborhood'],
                        'city'                 =>  $formAddress['city'],
                        'state'                =>  $formAddress['state'],
                        'complement'           =>  $formAddress['complement'],
                    ]
                );
                
                return response()->json([
                    'modified' => true,
                    'data'     => $response->data,    
                ]);
            }
            
            return response()->json([
                'modified' => false,
                'data'     => [],    
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

    private static function WBMCustomer($customer, $formCustomer)
    {
        $c1 = ($customer->fullname != $formCustomer['fullname']);            
        $c2 = ($customer->email != $formCustomer['email']);        
        $c3 = ($customer->birthdate != self::getBdayFormat($formCustomer['birthdate']));   
        $c4 = ($customer->ddd != substr($formCustomer['phone1'], 1, 2));     
        $c5 = ($customer->phone_number != substr($formCustomer['phone1'], 4, 13));
        $c6 = ($customer->secondary_phone_number !=  $formCustomer['phone2']);  
        $c7 = ($customer->cpf != $formCustomer['cpf']);      
        $c8 = ($customer->cep != $formCustomer['cep']); 

        if(env('PROFILE_PICTURE')){
            $c9 = ($customer->avatar != $formCustomer['avatar']); 
            return ($c1 || $c2 || $c3 || $c4 || $c5 || $c6 || $c7 || $c8 || $c9);
        }
        
        return ($c1 || $c2 || $c3 || $c4 || $c5 || $c6 || $c7 || $c8);
    }

    private static function WBMCustomerAddress($cAddress, $formAddress)
    {  
        $c1 = ($cAddress->cep != $formAddress['cep']);            
        $c2 = ($cAddress->street != $formAddress['street']);        
        $c3 = ($cAddress->number != $formAddress['number']);        
        $c4 = ($cAddress->reference_point != $formAddress['reference_point']);
        $c5 = ($cAddress->neighborhood !=  $formAddress['neighborhood']);  
        $c6 = ($cAddress->city != $formAddress['city']);      
        $c7 = ($cAddress->state != $formAddress['state']);
        $c8 = ($cAddress->complement != $formAddress['complement']);

        return ($c1 || $c2 || $c3 || $c4 || $c5 || $c6 || $c7 || $c8);
    }

    private static function getBdayFormat($birthdate)
    {
        $bday = explode("/", $birthdate);
        return ($bday[2] . '-' . $bday[1] . '-' . $bday[0]);
    }

    private static function verifySmartlook(){

        try {

            $response = self::executeSweetApi(
                'GET',
                '/api/v1/frontend/services-enabled-time/smartlook',
                []
            );
            
            return $response->data;

        } catch (ClientException $e) {
            $content = [];
            
            Log::debug("Client expection, request ->".Psr7\str($e->getRequest()));
            if ($e->hasResponse()) {
                Log::debug("Client expection, response ->".Psr7\str($e->getResponse()));
            }
            
            preg_match('/{.*}/i', $e->getMessage(), $content);

            Log::debug(print_r($content, true));        
                          
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

        catch (BadResponseException $e) 
        {
            Log::debug("Bad Response, request ->".Psr7\str($e->getRequest()));
            if ($e->hasResponse()) {
                Log::debug("Bad Response, response ->".Psr7\str($e->getResponse()));
            }
        } 
    }  
}
