<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DispatchFeaturedActionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actions:featured';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send to customers Featured Actions';

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
        $customers = Customer::where('confirmed', 1)->get();

        // foreach ($customers as $customer) {
        //     echo $customer->fullname;
        // }

        echo "Here!\n";
    }
}
