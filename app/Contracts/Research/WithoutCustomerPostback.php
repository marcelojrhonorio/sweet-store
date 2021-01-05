<?php

namespace App\Contracts\Research;

use GuzzleHttp\Client;
use App\Models\Customer;
use App\Models\Research;
use App\Models\ResearchPixel;
use App\Models\CheckinResearch;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\ClientException;
use App\Contracts\Research\ResearchPostbackStrategy;

class WithoutCustomerPostback implements ResearchPostbackStrategy{

    private $params;

    
    public function __construct($params){
        
        $this->params = $params;
        $this->research = Research::where('hasoffers_id', $this->params['hasoffers_id'])->first();

    }

    public function redirectUrl(){
        
        $pixel = $this->findPixel();
        
        return $pixel->link_redirect ?? '/';
    }

    public function runPixel(){

        try {

            if(false === $this->researchValidator()){
                 
                 Log::debug('Pesquisa inválida');
                 
                 return ('/');
     
            }
     
            $pixel = $this->findPixel();
     
                 $client = new Client();
     
                 switch($this->params['research_type']) {
                     case '1':
         
                        //  $pixelUrl = 'http://sweet.go2cloud.org/aff_lsr?offer_id=' . $this->params['hasoffers_id'] . '&transaction_id=' . $this->params['transaction_id'] . '&s_User_id=' . $this->params['customer_id'] . '&s_Offer_id=' . $this->params['hasoffers_id'] . '&s_Suplier_id=' . $this->params['affiliate_id'] . '&s_Type_id=' . $this->params['research_type'].'&s_Timestamp='.date('Y-m-d-H-i');
                        //  $response = $client->get($pixelUrl);
                        //  $res      = $response->getBody()->getContents();
         
                        //  if('success=true;' === $res) {
                        //      break;
                        //  }

                        //  $pixelUrl = 'http://sweet.go2cloud.org/aff_lsr?offer_id=' . $this->params['hasoffers_id'] . '&aff_id=' . $this->params['affiliate_id'] . '&s_User_id=' . $this->params['customer_id'] . '&s_Offer_id=' . $this->params['hasoffers_id'] . '&s_Suplier_id=' . $this->params['affiliate_id'] . '&s_Type_id=' . $this->params['research_type'].'&s_Timestamp='.date('Y-m-d-H-i');
                        //  $response = $client->get($pixelUrl);
                        //  $res      = $response->getBody()->getContents();
         
                        //  if('success=true;' === $res) {
                        //      break;
                        //  }    
         
                        //  Log::debug('Completed pixel failed');
     
                         break;
         
                     case '2':
         
                        //  $pixelUrl = 'http://sweet.go2cloud.org/aff_goal?a=lsr&goal_id='.$pixel['goal_id'].'&transaction_id=' . $this->params['transaction_id'] . '&s_User_id=' . $this->params['customer_id'] . '&s_Offer_id=' . $this->params['hasoffers_id'] . '&s_Suplier_id=' . $this->params['affiliate_id'] . '&s_Type_id=' . $this->params['research_type'];
                        //  $response = $client->get($pixelUrl);
                        //  $res      = $response->getBody()->getContents();
         
                        //  if('success=true;' === $res) {
                        //      break;
                        //  }

                        //  $pixelUrl = 'http://sweet.go2cloud.org/aff_goal?a=lsr&goal_id=' . $pixel['goal_id'] . '&aff_id=' . $this->params['affiliate_id'] . '&s_User_id=' . $this->params['customer_id'] . '&s_Offer_id=' . $this->params['hasoffers_id'] . '&s_Suplier_id=' . $this->params['affiliate_id'] . '&s_Type_id=' . $this->params['research_type'] . '&s_Timestamp='.date('Y-m-d-H-i');
                        //  $response = $client->get($pixelUrl);
                        //  $res      = $response->getBody()->getContents();
         
                        //  if('success=true;' === $res) {
                        //      break;
                        //  }                                                  
         
                        //  Log::debug('Quota full pixel failed');
     
                         break;
         
                     case '3':
         
                        //  $pixelUrl = 'http://sweet.go2cloud.org/aff_goal?a=lsr&goal_id='.$pixel['goal_id'].'&transaction_id=' . $this->params['transaction_id'] . '&s_User_id=' . $this->params['customer_id'] . '&s_Offer_id=' . $this->params['hasoffers_id'] . '&s_Suplier_id=' . $this->params['affiliate_id'] . '&s_Type_id=' . $this->params['research_type'];
                        //  $response = $client->get($pixelUrl);
                        //  $res      = $response->getBody()->getContents();
         
                        //  if('success=true;' === $res) {
                        //      break;
                        //  }

                        //  $pixelUrl = 'http://sweet.go2cloud.org/aff_goal?a=lsr&goal_id=' . $pixel['goal_id'] . '&aff_id=' . $this->params['affiliate_id'] . '&s_User_id=' . $this->params['customer_id'] . '&s_Offer_id=' . $this->params['hasoffers_id'] . '&s_Suplier_id=' . $this->params['affiliate_id'] . '&s_Type_id=' . $this->params['research_type'] . '&s_Timestamp='.date('Y-m-d-H-i');
                        //  $response = $client->get($pixelUrl);
                        //  $res      = $response->getBody()->getContents();
         
                        //  if('success=true;' === $res) {
                        //      break;
                        //  }                                                  
         
                        //  Log::debug('Filtered pixel failed');
     
                         break;
         
                     default:
         
                         $response = null;
                 }
          
                 return $this->redirectUrl();
     
            } catch (ClientException $e) {
                 $content = [];
     
                 preg_match('/{.*}/i', $e->getMessage(), $content);
     
                 return response()->json([
                     'status' => $e->getCode(),
                     'errors' => \GuzzleHttp\json_decode($content[0], true)['errors'],
                 ], 422);
             }
    }

    public function researchValidator(){
        
        $validator = true;
        
        if(empty($this->research)){
            
            Log::debug('Pesquisa não encontrada');

            $validator = false;

        }

        if(empty($this->findPixel())){

            Log::debug('Pixel não encontrado');

            $validator = false;            

        }

        return $validator;

    }    

    public function findPixel(){

        if(false === empty($this->research)){
            $pixel = ResearchPixel::where('research_id', $this->research->id)
                                        ->where('type', (int) $this->params['research_type'])
                                        ->first();            

            return $pixel;
        }

    }    

    
}