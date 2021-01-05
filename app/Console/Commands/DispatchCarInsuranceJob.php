<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;
use App\Events\CarInsuranceCreated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class DispatchCarInsuranceJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'car:lead {customerId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch CarInsuranceLeadJob';

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
        $customer = Customer::find($this->argument('customerId'));

        if (empty($customer)) {
            Log::debug('Customer not found...');
            return;
        }

        event(new CarInsuranceCreated($customer->id));
    }
}
