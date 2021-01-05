<?php

namespace App\Listeners;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Customer;
use App\Jobs\SocialClassJob;
use App\Traits\FixMailDomain;
use App\Jobs\MemberGetMemberJob;
use App\Jobs\SsiDispatchLeadJob;
use App\Jobs\SeguroAutoEmailJob;
use App\Jobs\RelationshipStepOne;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Events\CustomerVerifiedEvent;
use App\Events\CustomerCheckoutEvent;

class CustomerVerifiedListener
{
    use FixMailDomain;

    public $client;

    private $pixelCalogaUrl;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->pixelCalogaUrl = 'http://sweet.go2cloud.org/aff_lsr?offer_id=35&aff_id=1010';

        $this->client = new Client([
            'http_errors' => false,
            'headers' => [
                'cache-control' => 'no-cache',
            ],
        ]);
    }

    /**
     * Handle the event.
     *
     * @param  CustomerVerifiedEvent  $event
     */
    public function handle(CustomerVerifiedEvent $event)
    {
        
        $customer = $event->customer;

        //$this->doCheckin($customer->id);

        // SeguroAutoEmailJob::dispatch($customer)->onQueue('store_seguro_auto_email');

        // 1 dia
        RelationshipStepOne::dispatch($customer)
            ->onQueue('relationship')
            ->delay(now()->addDay());

        MemberGetMemberJob::dispatch($customer)
            ->onQueue('store_member_get_member')
            ->delay(now()->addDays(3));

        env('SSI_SUBMIT_SINGLE') ? SsiDispatchLeadJob::dispatch($customer->id)
            ->onQueue('dispatch_ssi_job') : false;

        if (env('CALOGA_INTEGRATION')) {
            $allowedBirthdate = self::allowedCalogaBirthdate($customer->birthdate);

            // Menor de 25 anos.
            if (false === $allowedBirthdate) { 
                return;
            }
    
            // Verifica se o e-mail é permitido.
            if ($this->isCalogaEmailBlocked($customer->email)) { 
                return;
            }
    
            // Verifica se existe mais usuários com o mesmo IP.
            $customersByIp = Customer::where('ip_address', $customer->ip_address)->get();
            if (sizeof($customersByIp) > 5) {
                Log::debug('[CALOGA] o IP ' . $customer->ip_address . ' aparece ' . sizeof($customersByIp) . ' vezes. O lead não será enviado.');
                return;
            }
    
            $splitedName = $this->splitName($customer->fullname);
            
            // Verifica se o nome ou sobrenome é vazio.
            if (('' == $splitedName[0]) || ('' == $splitedName[1])) {
                return;
            }
    
            $lead = [
                'gender' => $customer->gender === 'M' ? 'H' : 'F',
                'firstname' => urlencode($splitedName[0]),
                'lastname' => urlencode($splitedName[1]),
                'email' => $customer->email,
                'birthdate' => $customer->birthdate,
                'pc' => preg_replace('/\./', '', $customer->cep),
                'ip' => $customer->ip_address,
                'registerdate' => urlencode($customer->created_at),
                'source' => 'produtos',
            ];
    
            Log::debug('[CALOGA] nascimento lead enviado: ' . $lead['birthdate']);
            
            $url = env('API_CALOGA') . '?gender=' . $lead['gender'] . '&firstname=' . $lead['firstname'] . '&lastname=' . $lead['lastname'] . '&email=' . $lead['email'] . '&birthdate=' . $lead['birthdate'] . '&pc=' . $lead['pc'] . '&ip=' . $lead['ip'] . '&registerdate=' . $lead['registerdate'] . '&source=' . $lead['source'];
            $response = $this->client->get($url);
            $body = (string) $response->getBody();
    
            self::updateCalogaReturn($body ,$customer->id);
    
            if ('OK' === $body) {
                // $responsePixel = $this->client->get($this->pixelCalogaUrl);
                // $bodyPixel = (string) $responsePixel->getBody();
                Log::debug("Sucesso no caloga: {$url}");
            }
    
            if('Error'  === $body)
            {
                Log::debug("Error no caloga: {$url}");
            }
        }
    }

    private static function updateCalogaReturn(string $cReturn = null, int $customer_id = null)
    {
        $c = Customer::find($customer_id);
        $c->caloga_api_return = $cReturn;
        $c->update();
    }
    /**
     * @todo Add docs.
     */
    private function doCheckin($id)
    {
        $customer = Customer::find($id);

        if (!$customer->confirmed) {
            $customer->points += 30;
            $customer->save();
        }

        session(['points' => $customer->points]);
    }

    /**
     * @todo Add docs.
     */
    private function splitName($fullname)
    {
        $names = explode(' ', $fullname);

        $firstname = $names[0];

        unset($names[0]);

        $lastname = join(' ', $names);

        return [$firstname, $lastname];
    }

    private static function allowedCalogaBirthdate ($birthdate)
    {
        $years = Carbon::parse($birthdate)->age;

        if ($years >= 25) {
            return true;
        }

        return false;
    }
}
