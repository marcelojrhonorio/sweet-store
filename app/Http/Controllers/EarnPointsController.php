<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Traits\SweetStaticApiTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;

class EarnPointsController extends Controller
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
            'headers' => [
                'cache-control' => 'no-cache',
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ],
        ]);
    }

    public function index(Request $request)
    {
        if(env('SOCIAL_CLASS_RESEARCH') && self::verifyEnabled()) {
            if((783212 == session('id')) || (1187504 == session('id')) || (533419 == session('id')) || (69257 == session('id'))) {
                return view('earn', [
                    'socialClass' => env('SOCIAL_CLASS_RESEARCH') && self::verifyEnabled(),
                    'smartlook', true
                ]); 
            }

            return view('earn', [
                'socialClass' => env('SOCIAL_CLASS_RESEARCH') && self::verifyEnabled(),
                // 'smartlook', self::verifySmartlook()
             ]);
        }  
        
        if((705241 == session('id')) || (173588 == session('id')) || (3694 == session('id'))) {
            return view('earn', [               
                'smartlook', true
            ]); 
        }
        
        return view('earn', [
           // 'smartlook', self::verifySmartlook()
        ]);
    }    

    public function listByCategory(Request $request, $categoryId)
    {
        /**
         * Redirect to researches page :D
         * */
        
        if(4==$categoryId) {
            return redirect('researches');
        }
        if(6==$categoryId) {
            return redirect('stamps');
        }

        $endpoint = 'api/v1/actions/categories/' . $categoryId . '/actions';

        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . session()->get('token'),
            ],
        ];
       
        $response = $this->client->get($endpoint, $options);
        $json = \GuzzleHttp\json_decode($response->getBody()->getContents());

        $attributes = array();

        if (!empty($json) && property_exists($json, 'data')) {
            $attributes = $json->data;

        }

        //$actions = $this->getActions();
        $researchSteps = $this->verifyCustomerResearchSteps(session('id'));

        $research = $this->getResearch();

        $condition1 = env('RESEARCH_SEGURO_AUTO');
        $condition2 = isset($research) && empty($research);
        $condition3 = isset($research) && !empty($research) && 0 === $research->completed;

        $shouldShowCard = $condition1 && ($condition2 || $condition3);

        if(2==$categoryId) {

            $allActions = $this->getAllActions();

            if(env('SOCIAL_CLASS_RESEARCH') && self::verifyEnabled()) {
                return view('share-actions', [
                    'category' => 'Compartilhar',
                    'actions' => $allActions,
                    'socialClass' => env('SOCIAL_CLASS_RESEARCH') && self::verifyEnabled(),
                    'shouldShowCard' => $shouldShowCard
                ]);
            }

            return view('share-actions', [
                'category' => 'Compartilhar',
                'actions' => $allActions,
                'shouldShowCard' => $shouldShowCard
            ]);
        }

        if(env('SOCIAL_CLASS_RESEARCH') && self::verifyEnabled()) {
            if((705241 == session('id')) || (173588 == session('id')) || (3694 == session('id'))) {
                return view('earn', [
                    'attributes' => $attributes,
                    'shouldShowCard' => $shouldShowCard,
                    'socialClass' => env('SOCIAL_CLASS_RESEARCH') && self::verifyEnabled(),
                    'smartlook' => true
                ]);
            }
    
            return view('earn', [
                'attributes' => $attributes,
                'shouldShowCard' => $shouldShowCard,
                'socialClass' => env('SOCIAL_CLASS_RESEARCH') && self::verifyEnabled()
                //'smartlook' => self::verifySmartlook()
            ]);
        }

        if((705241 == session('id')) || (173588 == session('id')) || (3694 == session('id'))) {
            return view('earn', [
                'attributes' => $attributes,
                'shouldShowCard' => $shouldShowCard,
                'smartlook' => true
            ]);
        }

        return view('earn', [
            'attributes' => $attributes,
            'shouldShowCard' => $shouldShowCard,
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

    /**
     * @todo Add docs.
     */
    private function getResearch()
    {
        $endpoint = '/api/seguroauto/v1/frontend/customer-researches';

        $response = $this->client->get($endpoint . '?where[customer_id]=' . session('id'));

        $content = $response->getBody()->getContents();

        $dataJson = \GuzzleHttp\json_decode($content)->data;

        return $dataJson[0] ?? [];
    }

   private function getAllActions()
   {
        try {
            $response = self::executeSweetApi(
                'GET',
                '/api/v1/frontend/actions/getAllActions',
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
   
    private function getActions()
    {
        $endpoint = 'api/v1/actions';

        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . session()->get('token'),
            ],
        ];

        $response = $this->client->get($endpoint, $options);
        $json = \GuzzleHttp\json_decode($response->getBody()->getContents());       
        if (empty($json) || !property_exists($json,'data')) {
            return array();
        }

        return self::formatActions($json->data);
    }

    

    private function verifyCustomerResearchSteps($id)
    {
        try{

            $research = self::executeSweetApi(
                'GET',
                '/api/seguroauto/v1/frontend/customer-researches?where[customer_id]=' . $id,
                []
            );        

            if (empty($research->data)){
                return 'step_one';
            }          

            if ('0' === $research->data[0]->has_car){
                return 'finished';                
            }
          
            $customerResearchAnswer = self::executeSweetApi(
                'GET',
                '/api/seguroauto/v1/frontend/customer-research-answers?[customer_research_id]' . $research->data[0]->id,
                []
            );

            if (empty($customerResearchAnswer->data)){
                return 'step_two';
            }            

            $researchAnswer = self::executeSweetApi(
                'GET',
                '/api/seguroauto/v1/frontend/research-answer?where[research_id]=' . (string) $id,
                []
            );               
            
            if (empty($researchAnswer->data) && '0' === $customerResearchAnswer->data[0]->customer_research_answer_has_insurance){
                return 'influence_research';
            }              

            if ('0' === $research->data[0]->completed){
                return 'step_three';
            }

            return 'finished';

        } catch (ClientException $e) {
            $content = [];
            
            Log::debug("Client expection, request ->".Psr7\str($e->getRequest()));
            if ($e->hasResponse()) {
                Log::debug("Client expection, response ->".Psr7\str($e->getResponse()));
            }
            
            preg_match('/{.*}/i', $e->getMessage(), $content);

            Log::debug(print_r($content, true));
        
            return redirect('/seguro-auto/info/');          
        }
        catch (RequestException $e) 
        {            
            Log::debug("Request Expection , request ->".Psr7\str($e->getRequest()));
            if ($e->hasResponse()) {
                Log::debug("Request Expection ->".Psr7\str($e->getResponse()));
            }

            return redirect('/seguro-auto/info/');
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

    public static function formatActions ($actions) {
        $formatedActions = [];

        foreach ($actions as $action) {

            $ie = null;
            $c1 = 5 === (int) $action->action_type_id;

            /**
             * Se tiver uma ação do tipo Pesquisa Incentivada.
             */
            if ($c1) {
                $ie = self::getIncentiveEmail($action->action_type_metas[0]->value);
            }
            
            /**
             * Se tiver uma ação do tipo Pesquisa Incentivada
             * Mas não tiver nenhuma pesquisa incentivada relacionada.
             */
            if ($c1 && !$ie) {
                continue;
            }

            /**
             * Se já tiver um checkin do e-mail incentivado
             * sai fora do loop.
             */
            if ($ie && self::alreadyTaken($ie->id, session('id'))) {
                continue;
            }

            /**
             * Se tiver uma ação do tipo Pesquisa Incentivada
             * E tiver nenhuma pesquisa incentivada relacionada.
             */
            if ($c1 && $ie) {
                $action->grant_points = $ie->points;
                
                $param = (null !== $ie->code) ? 'incentive_email_code' : 'incentive_email_id';
                $identifier = (null !== $ie->code) ? $ie->code : $ie->id;
                
                $customersId = session('id');
                $action->action_type_metas[0]->value 
                    = env('APP_URL') . "/incentive-emails/postback?customers_id={$customersId}&{$param}={$identifier}";
            }

            array_push($formatedActions, $action);
        }

        return $formatedActions;
    }

    private static function getIncentiveEmail (string $link) {
        try {

            $param = (preg_match("/(?:incentive_email_code)/", $link)) ? 'code' : 'id';
            
            $identifier = 
                (preg_match("/(?:incentive_email_code)/", $link)) ? 
                explode('incentive_email_code=', $link)[1] : 
                explode('incentive_email_id=', $link)[1];

            $response = self::executeSweetApi(
                'GET',
                "/api/incentive/v1/frontend/incentive-emails?where[{$param}]={$identifier}",
                []
            );

            return $response->data[0] ?? null;
            
        } catch (RequestException $exception) {
            Log::debug($exception->getMessage());
        } catch (ConnectException $exception) {
            Log::debug($exception->getMessage());
        } catch (ClientException $exception) {
            Log::debug($exception->getMessage());
        }
    }

    private static function alreadyTaken (int $ieId, int $cId) {
        $response = self::executeSweetApi(
            'GET',
            "/api/incentive/v1/frontend/checkin?where[customers_id]={$cId}&where[incentive_emails_id]={$ieId}",
            []
        );

        return $response->data[0] ?? null;
    }

    /**
     * Social Class Static Methods.
     */
    private static function verifyEnabled() {
        try {

            $customerId = session()->get('id');

            $final = self::executeSweetApi(
                'GET',
                '/api/social-class/v1/frontend/final?where[customers_id]='.$customerId,
                []
            );

            /**
             * Se já tiver atualizado as informações pessoais.
             */
            if (strlen(session()->get('updated_personal_info_at')) < 1) {
                return false;
            }

            /**
             * Se não possuir registro em final_social_class.
             */
            if(false === isset($final->data[0]->id)){
                return true;
            }

            /**
             * Se a classe social ainda for nula.
             */
            if(isset($final->data[0]->id) && null == ($final->data[0]->final_class_by_questions)){
                return true;
            }

            /**
             * Se já ganhou a pontuação.
             */
            if(isset($final->data[0]->id) && (60 == $final->data[0]->earned_points)){
                return false;
            }

            return false;

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
