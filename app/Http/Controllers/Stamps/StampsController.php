<?php

namespace App\Http\Controllers\Stamps;

use Carbon\Carbon;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use App\Jobs\EmailStampJob;
use Illuminate\Http\Request;
use App\Traits\SweetStaticApiTrait;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;

class StampsController extends Controller
{
    use SweetStaticApiTrait;

    public function index () 
    {
        if(!env('STAMPS_VIEW'))
            return redirect('/');
        
        if((783212 == session('id')) || (1187504 == session('id')) || (533419 == session('id')) || (69257 == session('id'))) {
            return view('stamps', [
                'stamps' => self::getStamps(),
                'smartlook' => true
            ]);
        }

        return view('stamps', [
                    'stamps' => self::getStamps()
                    //'smartlook' => self::verifySmartlook()
        ]);
    }

    private static function verifySmartlook(){

        try {

            $response = self::executeSweetApi(
                'GET',
                '/api/v1/frontend/services-enabled-time/smartlook',
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

    private static function getStamps()
    {  
        try{
            $stampTypes = self::executeSweetApi(
                'GET',
                env('APP_SWEET_API') . "/api/stamp-types/v1/frontend/stamp-types",
                []
            );

            $allStamps = [];

            foreach($stampTypes->data as $types)
            {
                $typeStamp = self::getType($types->title);
                
                if(self::getTypeStatus($types->title))
                    continue;

                $stamps = self::getStampsByType($types->id);

                $cont = 0;
                foreach($stamps->data as $stamp)
                {
                    $progress = self::progressStamps($stamp->id);
        
                    if(false === $progress['already_taken']){
                        $stamps->data[$cont]->typeStamp = $typeStamp; 
                        $stamps->data[$cont]->progress_stamps = 0;
                        $stamps->data[$cont]->message_stamps = self::getMessageStamps(
                                $stamps->data[$cont]->stamp_types->title);
                    } else { 

                        if((($progress['progress_stamp'] >= 70) && ($progress['progress_stamp'] < 100)) 
                        && is_null($progress['customer_stamps']->send_email_at))
                        {
                            EmailStampJob::dispatch($progress['customer_stamps']->id)->onQueue('email_stamp_progress');
                            self::updateSendEmail($progress['customer_stamps']);                                                    
                        }                         

                        $stamps->data[$cont]->typeStamp = $typeStamp; 
                        $stamps->data[$cont]->progress_stamps = $progress['progress_stamp']; 
                        $stamps->data[$cont]->message_stamps = self::getMessageStamps(
                                $stamps->data[$cont]->stamp_types->title); 
                    }
                    $cont++;
                }
                array_push($allStamps, $stamps);           
            }

            return $allStamps;

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

    private static function updateSendEmail($customerStamps)
    {
        try {
            
            $response = self::executeSweetApi(
                'PUT',
                '/api/stamps/v1/frontend/customer-stamps/'.$customerStamps->id,
                [
                    'customers_id'    =>  $customerStamps->customers_id,
                    'stamps_id'       =>  $customerStamps->stamps_id,
                    'count_to_stamp'  =>  $customerStamps->count_to_stamp,
                    'send_email_at'   =>  Carbon::now()->toDateTimeString()
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

    private static function getTypeStatus($type)
    {
        if(('incentive_email' === $type) && !env('STAMP_EMAIL'))
        return true;
        if(('profile' === $type) && !env('STAMP_PROFILE'))
        return true;
    }

    private static function getStampsByType($idType)
    {
        try{
            $stampTypes = self::executeSweetApi(
                'GET',
                env('APP_SWEET_API') . "/api/stamps/v1/frontend/stamps?where[type]=".$idType,
                []
            );

            return $stampTypes;

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

    private static function progressStamps($id)
    {
        try{
            $customerStamps = self::executeSweetApi(
                'GET',
                env('APP_SWEET_API') . "/api/stamps/v1/frontend/customer-stamps?where[customers_id]=" . \Session::get('id') . 
                                                                            "&where[stamps_id]=" . $id,
                []
            );
        
            if(isset($customerStamps->data[0])){

                $total = $customerStamps->data[0]->stamp->required_amount;
                $feito = $customerStamps->data[0]->count_to_stamp;

                $prog = ($feito * 100) / $total; 

                return [
                    'already_taken'   => true,
                    'progress_stamp'  => floor($prog),
                    'customer_stamps' => $customerStamps->data[0]
                ];            

            } 

            return [ 
                'already_taken'   => false,
                'progress_stamp'  => 0,
                'customer_stamps' => []
            ];

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

    private static function getType($type)
    {        
        $action = '';
           
        switch($type){
          case 'action': 
          $action = 'Ação';
          break;
          
          case 'member_get_member':      
          $action = 'Compartilhamento';
          break;
          
          case 'email':  
          $action = 'Abertura de Email';         
          break; 
                      
          case 'incentive_email': 
          $action = 'Pesquisa por Email';    
          break;

          case 'profile': 
          $action = 'Completar perfil';    
          break;
        }
        return $action;
    }

    private static function getMessageStamps($type)
    {        
        $action = '';
        
        switch($type){
          case 'action': 
          $action = ' ações dentro deste portal';
          break;
          
          case 'member_get_member':              
          $action = ' indicações para a Sweet'; 
          break;
          
          case 'email':              
          $action = ' aberturas de email';       
          break; 
                      
          case 'incentive_email':              
          $action = ' pesquisas identificadas com o selo 🥇'; 
          break;

          case 'profile':              
          $action = ' atualização de perfil'; 
          break;
        }
        return $action;
    }

   

}
