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

class VerifyForwardingEmailSentJob implements ShouldQueue
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
        try {
            $response = self::executeSweetApi(
                'PUT',
                '/api/v1/frontend/customers/update-customer/reset-forwarding-email-sent',
                [
                    'customers_id' => $this->customers_id
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
}
