<?php

namespace App\Console\Commands;

use Log;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendEmailBackStoreJob;
use App\Traits\SweetStaticApiTrait;

class SendEmailBackStore extends Command
{
    use SweetStaticApiTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:back-to-the-store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch job for send email.';

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
            $job = (new SendEmailBackStoreJob($customer->id, $customer->fullname, $customer->email))->onQueue('send_email_back_to_the_store'); 
            dispatch($job);  
        }          
    }

    private static function getCustomers()
    {
        $now = now();

        $startFeature = env('START_FEATURE_BACK_TO_THE_STORE');        

        $customers = 
            DB::select("SELECT id, fullname, email 
                FROM sweet.customers 
                WHERE ((sweet.customers.last_store_login_at IS NOT NULL AND DATEDIFF('".$now."', sweet.customers.last_store_login_at) >= 15) OR (sweet.customers.last_store_login_at IS NULL AND DATEDIFF('".$now."', '". $startFeature ."') >= 15))
                    AND (sweet.customers.sent_store_email_at IS NULL OR DATEDIFF('".$now."', sweet.customers.sent_store_email_at) >= 15)
                    AND sweet.customers.deleted_at IS NULL AND id NOT IN ('SELECT sweet.unsubscribed_customers.customers_id 
                    FROM sweet.unsubscribed_customers')"); 

        return $customers;
    }    
}
