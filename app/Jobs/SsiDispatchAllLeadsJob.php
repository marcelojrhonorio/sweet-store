<?php

namespace App\Jobs;

use App\Jobs\SsiDispatchLeadJob;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SsiDispatchAllLeadsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $customers = Customer::where('confirmed', 1)
            ->where('ssi_status', 0)
            ->get();

        Log::debug("Total de custormers a serem processados no SSI! -> " . count($customers));

        foreach ($customers as $customer) {
            SsiDispatchLeadJob::dispatch($customer->id)
                ->onQueue('dispatch_ssi_job');
        }

        Log::debug('dispatch_ssi_single_lead_job_1 finished!');
    }
}
