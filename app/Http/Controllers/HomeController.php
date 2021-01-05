<?php
/**
 * @todo Add docs.
 */

namespace App\Http\Controllers;

use Session;
use Carbon\Carbon;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Traits\SweetStaticApiTrait;
use App\Traits\ReplaceAutofillLink;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;

/**
 * @todo Add docs.
 */
class HomeController extends Controller
{

    use SweetStaticApiTrait;
    use ReplaceAutofillLink;

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

    /**
     * @todo Add docs.
     */
    public function index()
    {   
        if(is_null(self::verifyUpdatedPersonalInfoAt(session('id'))) && session('confirmed'))
        {   
            return redirect('/profile')->with('alert', [
                'type'     => 'warning',
                'message1' => 'Cadastro incompleto!',
                'message2' => 'Preencha este formulário e ganhe o Selo',
                'message3' => 'Sweet Profile.',
            ]);
        }

        //mostrar tela de perfil a cada 30 dias
        if(self::verifyRedirectProfile(session('id'))) {
            return redirect('/profile')->with('alert', [
               'type'     => 'warning',
               'message1' => 'Atualize seu cadastro!',
               'message2' => 'Vá até o final deste formulário e nos informe seus ',
               'message3' => 'Interesses',
            ]);             
        }
            
        $actions = $this->getActions();
        $research = $this->getResearch();

        $condition1 = env('RESEARCH_SEGURO_AUTO');
        $condition2 = isset($research) && empty($research);
        $condition3 = isset($research) && !empty($research) && 0 === (int) $research->completed;

        $shouldShowCard = $condition1 && ($condition2 || $condition3);

        $researchSteps = $this->verifyCustomerResearchSteps(session('id'));

        if(env('SOCIAL_CLASS_RESEARCH') && self::verifyEnabled()) {
            if((783212 == session('id')) || (1187504 == session('id')) || (533419 == session('id')) || (69257 == session('id'))) {
                return view('index', [
                    'actions' => $actions,
                    'receive_offers' => session('receive_offers'),
                    'shouldShowCard' => $shouldShowCard,
                    'researchStep'   => $researchSteps,
                    'socialClass' => (env('SOCIAL_CLASS_RESEARCH') && self::verifyEnabled()),
                    'smartlook' => true
                ]);
            }
    
            return view('index', [
                'actions' => $actions,
                'receive_offers' => session('receive_offers'),
                'shouldShowCard' => $shouldShowCard,
                'researchStep'   => $researchSteps,
                'socialClass' => (env('SOCIAL_CLASS_RESEARCH') && self::verifyEnabled()),
            ]);
        } 

        if((705241 == session('id')) || (173588 == session('id')) || (3694 == session('id'))) {
            return view('index', [
                'actions' => $actions,
                'receive_offers' => session('receive_offers'),
                'shouldShowCard' => $shouldShowCard,
                'researchStep'   => $researchSteps,
                'smartlook' => true
            ]);
        }

        return view('index', [
            'actions' => $actions,
            'receive_offers' => session('receive_offers'),
            'shouldShowCard' => $shouldShowCard,
            'researchStep'   => $researchSteps,
        ]);
    }

    private static function verifyRedirectProfile($customers_id)
    {
        try {

            $response = self::executeSweetApi(
                'GET',
                '/api/v1/frontend/interest/schedule-updated-info/'.$customers_id,
                []
            );       
            
            if(now() >= $response->data) {               
                $date = now()->addDays(30);   
                self::updateScheduleUpdatedPersonalInfoAt($customers_id, $date);
                
                return true;
            }
            
            return false;
            
        } catch (RequestException $exception) {
            Log::debug($exception->getMessage());
        } catch (ConnectException $exception) {
            Log::debug($exception->getMessage());
        } catch (ClientException $exception) {
            Log::debug($exception->getMessage());
        }
    }

    private static function updateScheduleUpdatedPersonalInfoAt($customers_id, $date)
    {
        try {

            $response = self::executeSweetApi(
                'POST',
                '/api/v1/frontend/interest/schedule-updated-info/update',
                [
                    'customers_id' => $customers_id,
                    'date' => (string) $date,
                ]
            );
            
            return $response;
            
        } catch (RequestException $exception) {
            Log::debug($exception->getMessage());
        } catch (ConnectException $exception) {
            Log::debug($exception->getMessage());
        } catch (ClientException $exception) {
            Log::debug($exception->getMessage());
        }
    }

    public function fromShare(Request $request)
    {
        if(is_null(self::verifyUpdatedPersonalInfoAt(session('id'))) && session('confirmed'))
        {   
             return redirect('/profile')->with('alert', [
             'type'     => 'warning',
             'message1' => 'Cadastro incompleto!',
             'message2' => 'Preencha este formulário e ganhe o Selo',
             'message3' => 'Sweet Profile.',
             ]);
        }

        $actions = $this->getActions();
        $research = $this->getResearch();

        $condition1 = env('RESEARCH_SEGURO_AUTO');
        $condition2 = isset($research) && empty($research);
        $condition3 = isset($research) && !empty($research) && 0 === (int) $research->completed;

        $shouldShowCard = $condition1 && ($condition2 || $condition3);

        $researchSteps = $this->verifyCustomerResearchSteps(session('id'));

        if (empty(session('comingFromSocialActions'))) {
            return redirect('/');
        }

        $comingFromSocialActions = session('comingFromSocialActions');

        $domain = preg_match("/(?:uat-store)/", URL::current()) ? 'uat-store' : 'store';

        /**
         * Se usuário fez a ação (normal ou seguro auto) é redirecionado Home.
         * Caso contrário, é redirecionado para o link.
         * 
         */

        $verifyMGMAction = self::verifyMGMAction($comingFromSocialActions);         
        $verify = self::verifyStatusAction(session('id'), $comingFromSocialActions['action_id']);

        if(($verifyMGMAction && (null != $verifyMGMAction[0]->won_points)) || $verify->success) {
            return redirect('/');
        }

        //verificar member_get_member_action: ATUALIZAR WON_POINTS se null
        if($verifyMGMAction && (null === $verifyMGMAction[0]->won_points)) {
            $resp = self::updateWonPoints($comingFromSocialActions, $verifyMGMAction[0]->id);
        } else {
            $resp = self::verifyAction($comingFromSocialActions);
        }
        
        if(!is_null($resp['dataUrl'])) {
            return redirect($resp['dataUrl']);
        }

        if((705241 == session('id')) || (173588 == session('id')) || (3694 == session('id'))) {
            return view('index', [
                'actions'        => $actions,
                'shouldShowCard' => $shouldShowCard,
                'researchStep'   => $researchSteps,
                'params'         => $comingFromSocialActions,
                'smartlook'      => true
            ]);
        }

        return view('index', [
            'actions'        => $actions,
            'shouldShowCard' => $shouldShowCard,
            'researchStep'   => $researchSteps,
            'params'         => $comingFromSocialActions,
            //'smartlook'      => self::verifySmartlook()
        ]);
    }

    private static function updateWonPoints($comingFromSocialActions, int $id)
    {
        $customers_id = session('id');
        $indicated_by = $comingFromSocialActions['customer_id'];
        $action_id = $comingFromSocialActions['action_id']; 

        if('insurance_research' == $comingFromSocialActions['action_type']) { //SEGURO AUTO
            $action_type = 'Pesquisa Incentivada';

            //VERIFICAÇÃO SEGURO AUTO
            $res = self::verifySeguroAuto($customers_id);

            $comp0  = false;
            $comp1  = false;
            $crp0   = false;
            $crp100 = false; 

            // conditions seguro auto
            if($res) {
                $comp0  = ($res[0]->completed == 0);
                $comp1  = ($res[0]->completed == 1);
                $crp0   = ($res[0]->customer_research_points == 0);
                $crp100 = ($res[0]->customer_research_points == 100);
            }

            /**
             * se retornar registro do seguro auto e 
             * completed não for '0' e 
             * customer_research_points não for '0'
             */
            if(!is_null($res) && !($comp0 && $crp0)) { 

                //já fez seguro auto e será redirecionado para Home.
                if($comp1 && $crp100) {
                    return;
                }
            } 

        } else {
            $action_type = self::getActionType($comingFromSocialActions['action_type']);

            //VERIFY CHECKIN (ACTION)
            $verify = self::verifyStatusAction($customers_id, $action_id);

            //se o usuário ainda não fez a ação (
            if(!$verify->success) { 

                $dataMGMAction = [
                    'customers_id'      =>  $customers_id,
                    'indicated_by'      =>  $indicated_by,
                    'action_type'       =>  $action_type,
                    'action_id'         =>  $action_id,
                ];
                
                //FAZ REGISTRO NA TABELA CHECKINS 
                self::registerCheckin($customers_id, $action_id);                         
            }
        }

        try {

            if(session('confirmed') == 1) 
            {
                //atribui a pontuação ao usuário que compartilhou
                if($customers_id != $indicated_by) {
                    self::updateCustomerPoints($indicated_by, 5); 
                }  

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
            }             

            if('insurance_research' == $comingFromSocialActions['action_type']) { //SEGURO AUTO
                return [
                    'dataUrl'  => env('SWEETBONUS_URL'). '/seguro-auto/info/postback?customer_id=' . $customers_id
                ];

            } else {                
                //redireciona para o link da ação
                $urlAction = self::getUrlAction($action_id);
                if($urlAction->data) {
                    return [
                        'dataUrl'  => $urlAction->data
                    ]; 
                }
            }       

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

    private static function verifyMGMAction($comingFromSocialActions)
    {
        $customers_id = session('id');
        $indicated_by = $comingFromSocialActions['customer_id'];
        $action_id = $comingFromSocialActions['action_id'];   

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

    private static function verifyAction($comingFromSocialActions)
    {
        $customers_id = session('id');
        $indicated_by = $comingFromSocialActions['customer_id'];
        $action_id = $comingFromSocialActions['action_id'];

        if('insurance_research' == $comingFromSocialActions['action_type']) { //SEGURO AUTO
            $action_type = 'Pesquisa Incentivada';

            /**
             * VERIFY SEGURO AUTO
             * Se já fez, redireciona pra Home. 
             * Se não fez, insere registro na tabela
             * 'member_get_member_action' e redireciona para o link do Seguro Auto. 
             */
            $res = self::verifySeguroAuto($customers_id);

            $comp0  = false;
            $comp1  = false;
            $crp0   = false;
            $crp100 = false; 

            // conditions seguro auto
            if($res) {
                $comp0  = ($res[0]->completed == 0);
                $comp1  = ($res[0]->completed == 1);
                $crp0   = ($res[0]->customer_research_points == 0);
                $crp100 = ($res[0]->customer_research_points == 100);
            }

            /**
             * se retornar registro do seguro auto e 
             * completed não for '0' e 
             * customer_research_points não for '0'
             */
            if(!is_null($res) && !($comp0 && $crp0)) { 

                //já fez seguro auto e será redirecionado para Home.
                if($comp1 && $crp100) {
                    return;
                }
            } 

            $MGMAction = [
                'customers_id'      =>  $customers_id,
                'indicated_by'      =>  $indicated_by,
                'action_type'       =>  $action_type,
                'action_id'         =>  $action_id,
            ];
            
            if($customers_id == $indicated_by) {
                return;
            }

            if(session('confirmed') == 1) {
                self::insertMGMAction($MGMAction);
            }

            //redireciona para o link do seguro auto
            return [
                'dataUrl'  => env('SWEETBONUS_URL'). '/seguro-auto/info/postback?customer_id=' . $customers_id
            ];

            /**
             * se não entrar no IF (retornou null do seguro auto ou retornou registro do seguro auto
             * mas completed e customer_research_points são '1' nem '100'[status:finalizado] )
             * segue o fluxo, inserindo na registro tabela member_get_member_action
             * e redirecionando para o link do seguro auto. 
             * (lá já faz o tratamento de inserir na tabela sweet_seguro_auto.customer_researches ) 
             */

        } else {
            $action_type = self::getActionType($comingFromSocialActions['action_type']);
        }
        
        //VERIFY CHECKIN (ACTION)
        $verify = self::verifyStatusAction($customers_id, $action_id);

        //se o usuário ainda não fez a ação 
        if(!$verify->success) { 

            $dataMGMAction = [
                'customers_id'      =>  $customers_id,
                'indicated_by'      =>  $indicated_by,
                'action_type'       =>  $action_type,
                'action_id'         =>  $action_id,
            ];
            
            //FAZ REGISTRO NA TABELA MEMBER_GET_MEMBER_ACTIONS
            if($customers_id == $indicated_by) {
                return;
            }
            
            if(session('confirmed') == 1) {
                self::insertMGMAction($dataMGMAction);                  
            }            

            //FAZ REGISTRO NA TABELA CHECKINS 
            self::registerCheckin($customers_id, $action_id);   
            
            //redireciona para o link da ação
            $urlAction = self::getUrlAction($action_id);
            if($urlAction->data) {
                return [
                    'dataUrl'  => $urlAction->data
                ];    
            }             
        } 
        return;
    }

    private static function insertMGMAction($params)
    {
        try {
           
            $response = self::executeSweetApi(
                'POST',
                '/api/share-action/v1/frontend/share-action/',
                [
                    'customers_id'      =>  $params['customers_id'],
                    'indicated_by'      =>  $params['indicated_by'],
                    'action_type'       =>  $params['action_type' ],
                    'action_id'         =>  $params['action_id'   ],                   
                    'won_points'        =>  Carbon::now()->toDateTimeString(),
                ]
            );
            
            //atribui a pontuação ao usuário que compartilhou
            self::updateCustomerPoints($params['indicated_by'], 5);  
    
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

    private static function registerCheckin(int $customer_id, int $action_id)
    {
        try {
           
            $response = self::executeSweetApi(
                'POST',
                '/api/v1/checkin/',
                [
                    'customer_id' => $customer_id,
                    'action_id'   => $action_id,
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

    private static function getUrlAction(int $action_id) 
    {
        try {            
            $response = self::executeSweetApi(
                'GET',
                '/api/v1/frontend/actions/type-metas/'. $action_id,
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

    private static function verifySeguroAuto(int $customer_id)
    {
        try {            
            $response = self::executeSweetApi(
                'GET',
                '/api/seguroauto/v1/frontend/customer-researches?where[customer_id]='. $customer_id,
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
            

    private static function verifyStatusAction(int $customer_id, int $action_id)
    {
        /**
         * verifica se usuário fez a ação.
         */
        try {            
            $response = self::executeSweetApi(
                'GET',
                '/api/v1/checkin/getCheckin?customer_id='. $customer_id . '&action_id=' . $action_id,
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

    private static function getActionType(int $action_type)
    {
        /**
         * verifica tipo de ação.
         */
        try {            
            $response = self::executeSweetApi(
                'GET',
                '/api/v1/frontend/actions/type/'. $action_type,
                []
            );
           
            return $response->data->name;
                
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

    private static function verifyUpdatedPersonalInfoAt($id)
    {
        try {
            
            $response = self::executeSweetApi(
                'POST',
                '/api/v1/frontend/customers/'. $id,
                []
            );
           
            return $response->customer->updated_personal_info_at;
                
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
     * @todo Add docs.
     */

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
                if(!isset($action->action_type_metas[0])) {
                    continue;
                }

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
             * E tiver pesquisa incentivada relacionada.
             */
            if ($c1 && $ie) {
                $action->grant_points = $ie->points;
                
                $param = (null !== $ie->code) ? 'incentive_email_code' : 'incentive_email_id';
                $identifier = (null !== $ie->code) ? $ie->code : $ie->id;
                
                $customersId = session('id');
                $action->action_type_metas[0]->value 
                    = env('APP_URL') . "/incentive-emails/postback?customers_id={$customersId}&{$param}={$identifier}";
            }

            /**
             * Formatar o link se a ação for do tipo autofill.
             */
            if (6 === (int) $action->action_type_id) {
                $action->action_type_metas[0]->value 
                    = self::handleReplaceLink($action->action_type_metas[0]->value);
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
                explode('incentive_email_code=', $link)[0] : 
                explode('incentive_email_id=', $link)[0];

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

           // Log::debug(get_object_vars($final));

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
            if(isset($final->data[0]->id) && (null == $final->data[0]->final_class_by_questions)){
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