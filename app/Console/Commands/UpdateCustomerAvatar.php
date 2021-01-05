<?php

namespace App\Console\Commands;

use Log;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Traits\SweetStaticApiTrait;
use App\Jobs\UpdateCustomerAvatarJob;

class UpdateCustomerAvatar extends Command
{
    use SweetStaticApiTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:customer-avatar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch job for verify avatar from customers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
         //get customers from the conditions
         $customers = self::getCustomers();

         foreach ($customers as $customer) {
            $job = (new UpdateCustomerAvatarJob($customer->id))->onQueue('verify_customer_avatar'); 
            dispatch($job);  
         }
    }

    private static function getCustomers()
    {
        try {
            $response = self::executeSweetApi(
                'GET',
                '/api/v1/frontend/customers/update-customer/verify-customer-avatar',
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
