<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Services\SsiService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SsiDispatchLeadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $customer_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($customer_id = 0)
    {
        //
        $this->customer_id = $customer_id ;
     
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ssiService = new SsiService($this->customer_id);
        if ($ssiService->leadDispatch()) {
            return true;
        }
        return false;
    }
}
