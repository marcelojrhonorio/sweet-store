<?php

namespace App\Console\Commands;

use Log;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Jobs\VerifyForwardingEmailSentJob;
use App\Traits\SweetStaticApiTrait;

class VerifyForwardingEmailSent extends Command
{
    use SweetStaticApiTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:forwarding-email-sent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch job for verify forwarding_email_send';

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
            $job = (new VerifyForwardingEmailSentJob($customer->id))->onQueue('verify_forwarding_email_sent'); 
            dispatch($job);  
        }     
    }

    private static function getCustomers()
    {
        try {
            $response = self::executeSweetApi(
                'GET',
                '/api/v1/frontend/customers/update-customer/verify-forwarding-email-send',
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
