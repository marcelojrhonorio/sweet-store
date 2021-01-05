<?php

namespace App\Jobs;

use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Traits\SweetStaticApiTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Exception\BadResponseException;

class UpdateCustomerAvatarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use SweetStaticApiTrait;
    
    private $customers_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($customers_id)
    {
        $this->customers_id = $customers_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $customer = self::getCustomer($this->customers_id);

        if(preg_match('/asid=/', $customer->avatar)) 
        {
            $aux1 = explode("asid=", $customer->avatar);
            $aux2 = explode("&", $aux1[1]);

            $customer = self::updateCustomerAvatar($this->customers_id, $aux2[0]);
        } else {
            $customer = self::updateCustomerAvatar($this->customers_id, null);
        }        
        
    }

    private static function updateCustomerAvatar($customers_id, $userIdAvatar)
    {
        try {
            $response = self::executeSweetApi(
                'POST',
                '/api/v1/frontend/customers/update-customer/update-customer-avatar',
                [
                    'customers_id' => $customers_id,
                    'userIdAvatar' => $userIdAvatar,
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

    private static function getCustomer($customers_id)
    {
        try {
            $response = self::executeSweetApi(
                'POST',
                '/api/v1/frontend/customers/'.$customers_id,
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
}
