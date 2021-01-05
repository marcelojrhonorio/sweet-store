<?php

namespace App\Contracts\Research;

use GuzzleHttp\Client;
use App\Models\Customer;
use App\Models\Research;
use App\Models\ResearchPixel;
use App\Models\CheckinResearch;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\ClientException;
use App\Services\ResearchDoubleOptinService;
use App\Contracts\Research\ResearchPostbackStrategy;

class WithCustomerPostback implements ResearchPostbackStrategy{
    
    private $params;
    
    
    public function __construct($params){
        
        $this->params = $params;
        $this->customer = Customer::find($this->params['customer_id']);
        $this->research = Research::where('hasoffers_id', $this->params['hasoffers_id'])->first();
        
    }
    
    
    public function redirectUrl(){
        
        $this->createCheckin();          
        
        $pointsToUrl = $this->earnPoints();
        
        $this->setCurrentUser(); 

        if($this->params['confirmed']){
        
            $doubleOptin = new ResearchDoubleOptinService($this->params);
        
            $doubleOptin->confirm();
        
        }
        
        $pixel = $this->findPixel();
        
        return $pointsToUrl;       
        
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
                
                // $pixelUrl = 'http://sweet.go2cloud.org/aff_lsr?offer_id=' . $this->params['hasoffers_id'] . '&transaction_id=' . $this->params['transaction_id'] . '&s_User_id=' . $this->params['customer_id'] . '&s_Offer_id=' . $this->params['hasoffers_id'] . '&s_Suplier_id=' . $this->params['affiliate_id'] . '&s_Type_id=' . $this->params['research_type'].'&s_Timestamp='.date('Y-m-d-H-i');
                // $response = $client->get($pixelUrl);
                // $res      = $response->getBody()->getContents();
                
                // if('success=true;' === $res) {
                //     break;
                // }
                
                // $pixelUrl = 'http://sweet.go2cloud.org/aff_lsr?offer_id=' . $this->params['hasoffers_id'] . '&aff_id=' . $this->params['affiliate_id'] . '&s_User_id=' . $this->params['customer_id'] . '&s_Offer_id=' . $this->params['hasoffers_id'] . '&s_Suplier_id=' . $this->params['affiliate_id'] . '&s_Type_id=' . $this->params['research_type'].'&s_Timestamp='.date('Y-m-d-H-i');
                // $response = $client->get($pixelUrl);
                // $res      = $response->getBody()->getContents();
                
                // if('success=true;' === $res) {
                //     break;
                // }
                
                // Log::debug('Completed pixel failed: ' . $res);
                
                break;
                
                case '2':
                
                // $pixelUrl = 'http://sweet.go2cloud.org/aff_goal?a=lsr&goal_id='.$pixel['goal_id'].'&transaction_id=' . $this->params['transaction_id'] . '&s_User_id=' . $this->params['customer_id'] . '&s_Offer_id=' . $this->params['hasoffers_id'] . '&s_Suplier_id=' . $this->params['affiliate_id'] . '&s_Type_id=' . $this->params['research_type'];
                // $response = $client->get($pixelUrl);
                // $res      = $response->getBody()->getContents();
                
                // if('success=true;' === $res) {
                //     break;
                // }
                
                // $pixelUrl = 'http://sweet.go2cloud.org/aff_goal?a=lsr&goal_id=' . $pixel['goal_id'] . '&aff_id=' . $this->params['affiliate_id'] . '&s_User_id=' . $this->params['customer_id'] . '&s_Offer_id=' . $this->params['hasoffers_id'] . '&s_Suplier_id=' . $this->params['affiliate_id'] . '&s_Type_id=' . $this->params['research_type'] . '&s_Timestamp='.date('Y-m-d-H-i');
                // $response = $client->get($pixelUrl);
                // $res      = $response->getBody()->getContents();
                
                // if('success=true;' === $res) {
                //     break;
                // }                    
                
                // Log::debug('Quota full pixel failed: ' . $res);
                
                break;
                
                case '3':
                
                // $pixelUrl = 'http://sweet.go2cloud.org/aff_goal?a=lsr&goal_id='.$pixel['goal_id'].'&transaction_id=' . $this->params['transaction_id'] . '&s_User_id=' . $this->params['customer_id'] . '&s_Offer_id=' . $this->params['hasoffers_id'] . '&s_Suplier_id=' . $this->params['affiliate_id'] . '&s_Type_id=' . $this->params['research_type'];
                // $response = $client->get($pixelUrl);
                // $res      = $response->getBody()->getContents();
                
                // if('success=true;' === $res) {
                //     break;
                // }
                
                // $pixelUrl = 'http://sweet.go2cloud.org/aff_goal?a=lsr&goal_id=' . $pixel['goal_id'] . '&aff_id=' . $this->params['affiliate_id'] . '&s_User_id=' . $this->params['customer_id'] . '&s_Offer_id=' . $this->params['hasoffers_id'] . '&s_Suplier_id=' . $this->params['affiliate_id'] . '&s_Type_id=' . $this->params['research_type'] . '&s_Timestamp='.date('Y-m-d-H-i');
                // $response = $client->get($pixelUrl);
                // $res      = $response->getBody()->getContents();
                
                // if('success=true;' === $res) {
                //     break;
                // }                    
                
                // Log::debug('Filtered pixel failed: ' . $res);
                
                break;
                
            }
            
            // if (false === ('success=true;' === $res)){
                
            //     Log::debug('Erro ao saltar o pixel da pesquisa');                
                
            //     $pixel = $this->findPixel();
                
            //     return $pixel->link_redirect ?? '/';
                
            // }
            
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
        
        if(false === empty($this->alreadyTaken()) && false === empty($this->research)){
            
            Log::debug('O usuário ' . $this->customer->id . ' já respondeu a pesquisa');
            
            $validator = false;
        }
        
        if(empty($this->research)){
            
            Log::debug('Pesquisa não encontrada');
            
            $validator = false;
            
        }
        
        if(empty($this->findPixel())){
            
            Log::debug('Pixel não encontrado');
            
            $validator = false;            
            
        }
        
        if(empty($this->customer)){
            
            Log::debug('Usuário ' . $this->params['customer_id'] . ' não encontrado na base');
            
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
    
    public function alreadyTaken(){                     
        
        if(false === empty($this->research) && false === empty($this->customer)){
            
            $alreadyTaken = CheckinResearch::where('researches_id', $this->research->id)
            ->where('customers_id', $this->customer->id)
            ->first();
            
            return $alreadyTaken;
        }
        
    }    
    
    public function createCheckin()
    {
        
        $checkin = CheckinResearch::create(
            [
                'customers_id'  => $this->customer->id,
                'researches_id' => $this->research->id,
                ]
            );
            
            if (empty($checkin)) {
                Log::debug('Erro ao fazer checkin da pesquisa' . $this->customer->id . ' para o usuário ' . $this->customer->id);
            }        
            
        }
        
        public function earnPoints(){
            
            $previousBalance = $this->customer->points;
            
            $researchPoints =0;
            
            if (1 === (int) $this->params['research_type']) {
                $this->customer->points = (int) $this->customer->points + (int) $this->research->points;
                $researchPoints = $this->research->points;
            }
            
            $token = base64_encode(str_random(40));
            
            $this->customer->token = $token;
            
            $this->customer->save();
            
            return  [
                'previousBalance' => $previousBalance,
                'finalPoints'     => $this->customer->points,
                'researchPoints'  =>$researchPoints,
            ];
            
        }
        
        public function setCurrentUser(){
            session([
                'id'        => $this->customer->id,
                'name'      => $this->customer->fullname,
                'email'     => $this->customer->email,
                'birthdate' => $this->customer->birthdate,
                'gender'    => $this->customer->gender,
                'cep'       => $this->customer->cep,
                'avatar'    => $this->customer->avatar,
                'points'    => $this->customer->points,
                'token'     => $this->customer->token,
                'confirmed' => $this->customer->confirmed,
                ]);        
            }
            
        }