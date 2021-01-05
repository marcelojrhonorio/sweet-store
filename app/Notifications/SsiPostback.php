<?php

namespace App\Notifications;


use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use App\Traits\SweetStaticApiTrait;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;

class SsiPostback extends Notification
{
    use SweetStaticApiTrait;

    use Queueable;

    private $params;
    private $postback;
    private $fullUrl;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($params, $postback, $fullUrl)
    {
       $this->params   = $params;
       $this->postback = $postback;
       $this->customer = null;
       $this->fullUrl  = $fullUrl;
    }


   

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    
    
    public function toSlack($notifiable)
    {
        $this->customer = self::getCustomer($this->params['sourcePID'])->customer ?? "Não informado";

        $par = $this->params;

       //dd($customer->customer->fullname);

        return (new SlackMessage)
            ->success()
            ->content("Retorno de pesquisa da SSI")
            ->attachment(function ($attachment) use ($par) {
                $attachment->title("Projeto Nº ". $par['projectID'])
                            ->fields([
                                'ID do Usuário'         => $this->params['sourcePID'],
                                'Nome'                  => $this->customer->fullname ?? "Não informado",
                                'E-mail'                => $this->customer->email ?? "Não informado",  
                                'Resultado da pesquisa' => $this->postback,
                                'URL de postback'       => $this->fullUrl,
                            ]);
            });
    }

   

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
   /*  public function toArray($notifiable)
    {
        return [
            //
        ];
    }*/

    private static function getCustomer(int $id)
    {
        try{

            $response = self::executeSweetApi(
                'POST',
                '/api/v1/frontend/customers/' . $id,
                []
            );

            return $response;

        } catch (ClientException $e) {
            $content = [];
            
            Log::debug("Client expection, request ->".Psr7\str($e->getRequest()));
            if ($e->hasResponse()) {
                Log::debug("Client expection, response ->".Psr7\str($e->getResponse()));
            }
            
            preg_match('/{.*}/i', $e->getMessage(), $content);

            Log::debug(print_r($content, true));        
                        
        }
        catch (RequestException $e) 
        {            
            Log::debug("Request Expection , request ->".Psr7\str($e->getRequest()));
            if ($e->hasResponse()) {
                Log::debug("Request Expection ->".Psr7\str($e->getResponse()));
            }
        } 
        catch (ConnectException $e) 
        {
            Log::debug("Connection expection, request ->".Psr7\str($e->getRequest()));
            if ($e->hasResponse()) {
                Log::debug("Connection expection, response ->".Psr7\str($e->getResponse()));
            }
        } 

        catch (BadResponseException $e) 
        {
            Log::debug("Bad Response, request ->".Psr7\str($e->getRequest()));
            if ($e->hasResponse()) {
                Log::debug("Bad Response, response ->".Psr7\str($e->getResponse()));
            }
        } 
    }

}
