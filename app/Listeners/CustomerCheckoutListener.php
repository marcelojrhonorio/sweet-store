<?php

namespace App\Listeners;

use GuzzleHttp\Psr7;
use App\Models\Customer;
use App\Jobs\WonStampJob;
use App\Traits\SweetStaticApiTrait;
use Illuminate\Support\Facades\Log;
use App\Events\CustomerCheckoutEvent;
use Illuminate\Queue\InteractsWithQueue;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Queue\ShouldQueue;
use GuzzleHttp\Exception\BadResponseException;

class CustomerCheckoutListener
{
    use SweetStaticApiTrait;
    
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CustomerCheckoutEvent  $event
     * @return void
     */
    public function handle(CustomerCheckoutEvent $event)
    {
        $customerId = (int) $event->customerId;
        $actionId   = (int) $event->actionId;
        $actionType = self::getActionTypeId($event->actionType);

        $stamps         = self::getStamps($customerId, $actionType);
        $customerStamps = self::getCustomerStamps($customerId, $actionType);

        /**
         * Se existir um selo cadastrado para o $actionType
         * e o usuário ainda não fez nenhuma ação para tal.
         */
        if ((0 === count($customerStamps) && $stamps) && env('STAMPS_VIEW')) {

            if(self::isStampTypeEnabled($actionType)) {
               self::createCustomerStamp($stamps[0]->id, $customerId);
            }            
            self::saveCustomerAction($customerId, $actionType, $actionId);                    
        }

        /**
         * Se existir um selo cadastrado para o $actionType
         * e o usuário já fez mo mínimo uma ação para tal.
         */
        if ((0 !== count($customerStamps) && $stamps) && env('STAMPS_VIEW')) {

            if(self::isStampTypeEnabled($actionType)) {
               self::updateCustomerStamp($customerStamps, $actionId);
            }
            self::saveCustomerAction($customerId, $actionType, $actionId);
        }

        /**
         * Se não existir nenhum selo cadastrado para o $actionType
         * e consequentemente o usuário não fez nenhuma ação para tal.
         * Nesse caso, é creditado apenas a pontuação da ação.
         */
        if ((!$customerStamps && !$stamps && env('STAMPS_VIEW')) || !env('STAMPS_VIEW')) {

            self::saveCustomerAction($customerId, $actionType, $actionId);
        }

    }

    private static function isStampTypeEnabled($actionType)
    {
        if((3 === $actionType) && env('STAMP_EMAIL'))
        return true;   

        if((4 === $actionType) && env('MEMBER_GET_MEMBER'))
        return true; 
        
        if((5 === $actionType) && env('STAMP_PROFILE'))
        return true; 

        return false;
    }

    private static function getStamps(int $customerId, string $actionType) {
        
        try {

            $stamps = self::executeSweetApi(
                'GET',
                '/api/stamps/v1/frontend/stamps?where[type]=' . $actionType,
                []
            );
   
            $s = [];
            $stamps = $stamps->data;

            $i = 0;

            for ($i; $i < count($stamps); $i++) {
                $customerStamps = self::executeSweetApi(
                    'GET',
                    '/api/stamps/v1/frontend/customer-stamps?where[customers_id]=' . $customerId .
                    '&where[stamps_id]=' . $stamps[$i]->id,
                    []
                );

                $countToStamp = isset($customerStamps->data[0]->count_to_stamp) ? (int) $customerStamps->data[0]->count_to_stamp : 'empty';

                if (is_string($countToStamp) || (int) $customerStamps->data[0]->count_to_stamp < $stamps[$i]->required_amount) {
                    array_push($s, $stamps[$i]);
                }
            }

            return $s;

        } catch (RequestException $exception) {
            Log::debug($exception->getMessage());
        } catch (ConnectException $exception) {
            Log::debug($exception->getMessage());
        } catch (ClientException $exception) {
            Log::debug($exception->getMessage());
        }
    }

    private static function getCustomerStamps(int $customerId, string $actionType) {

        try {
            
            $customerStamps = self::executeSweetApi(
                'GET',
                '/api/stamps/v1/frontend/customer-stamps?where[customers_id]=' . $customerId,
                []
            );

            $cs = [];
            $customerStamps = $customerStamps->data;

            $i = 0;

            for ($i; $i < count($customerStamps); $i++) {
                
                $condition1 = $customerStamps[$i]->stamp->type === $actionType;
                $condition2 = (int) $customerStamps[$i]->stamp->required_amount > 
                              (int) $customerStamps[$i]->count_to_stamp;

                if ($condition1 && $condition2) {
                    array_push($cs, $customerStamps[$i]);
                }
            }

            return $cs;

        } catch (RequestException $exception) {
            Log::debug($exception->getMessage());
        } catch (ConnectException $exception) {
            Log::debug($exception->getMessage());
        } catch (ClientException $exception) {
            Log::debug($exception->getMessage());
        }
    }

    private static function saveCustomerAction(int $customerId, string $actionType, int $actionId) {
           
        switch ($actionType) {

            /**
            * Registro na tabela `checkins`
            */
            case '1':
            break;

            /**
             * Registro na tabela `customers`, na coluna
             * `count_opened_email`.
             */
            case '2':
            break;

            /**
             * Registro na tabela `checkin_incentive_emails`
             */
            case '3': 
                $points = (int) self::getIncentiveEmailPoints($actionId);
                self::updateCustomerPoints($customerId, $points);  
            break;
            /**
             * Apenas atualiza a quantidade de pontos.
             */
            case '4': 
                self::updateCustomerPoints ($customerId, 10);                
            break;

            case '5': 
                self::updateCustomerPoints ($customerId, 0);                
            break;
           

           
        }
    }

    private static function getIncentiveEmailPoints($id)
    {
        try {
            
            $points = self::executeSweetApi(
                'GET',
                '/api/incentive/v1/frontend/incentive-emails?where[id]='.$id,
                []
            );

            return $points->data[0]->points;

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

    private static function createCustomerStamp(int $stampId, int $customerId)
    {
        try {

            $response = self::executeSweetApi(
                'POST',
                '/api/stamps/v1/frontend/customer-stamps/',
                [
                    'customers_id' => $customerId,
                    'stamps_id' => $stampId,
                    'count_to_stamp' => 1
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

    private static function updateCustomerStamp($customerStamps, int $actionId)
    {
        $i = 0;
        $customerStamp = '';

        for ($i; $i <= count($customerStamps); $i++) {
            if ((int) $customerStamps[$i]->count_to_stamp < (int) $customerStamps[$i]->stamp->required_amount) {
                
                $customerStamp = $customerStamps[$i];
                break;

            }
        }

        if ("" === $customerStamp) {

            self::saveCustomerAction (
                    $customerStamps[1]->customers_id, 
                    $customerStamps[1]->stamp->type, 
                    $actionId
                );

            return $customerStamp;

        }

        try {

            $customerStamp->count_to_stamp += 1;

            $response = self::executeSweetApi(
                'PUT',
                '/api/stamps/v1/frontend/customer-stamps/' . $customerStamp->id,
                $customerStamp
            );

            /**
             * Se o usuário atingiu a quantidade de ações para ganhar o selo,
             * irá disparar a Job informando que ele ganhou, além de atualizar 
             * sua pontuação.
             */
            if ((int) $customerStamp->stamp->required_amount === $response->data->count_to_stamp) {
                self::wonStamp($customerStamp);
            }

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

    private static function updateCustomerPoints(int $customerId, int $points) {

        $customer = Customer::find($customerId);
        $customer->points += $points;

        $customer->save();

        return $customer;
    }

    private static function getCustomer(int $id) {

        try {
            $response = self::executeSweetApi(
                'POST',
                '/api/v1/frontend/customers/' . $id,
                []
            );
    
            return $response->customer;

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

    private static function wonStamp($customerStamp)
    {
        WonStampJob::dispatch($customerStamp->id)->onQueue('customer_won_stamp');
    }

    private static function getActionTypeId(string $actionType)
    {
        $stampType = self::executeSweetApi(
            'GET',
            env('APP_SWEET_API') . "/api/stamp-types/v1/frontend/stamp-types?where[title]=".$actionType,
            []
        );

        return $stampType->data[0]->id; 
    }
}
