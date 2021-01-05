<?php

namespace App\Console\Commands;

use App\Jobs\SsiDispatchAllLeadsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;

class DispatchSsiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ssi:lead';
    // protected $signature = 'ssi:lead {customerId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch SsiJob';

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
        SsiDispatchAllLeadsJob::dispatch()->onQueue('ssi_dispatch_all_leads_job');        
    }
}
