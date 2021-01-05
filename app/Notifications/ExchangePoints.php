<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;

class ExchangePoints extends Notification
{
    use Queueable;

    private $exchange;

     /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($exchange)
    {
        $this->exchange = $exchange;
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
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        $url = env('APP_STORAGE') . '/exchanges/edit/' . $this->exchange->id;

        return (new SlackMessage)
            ->success()
            ->content('Uma nova troca de pontos foi solicitada.')
            ->attachment(function ($attachment) use ($url) {
                $attachment->title('Troca de pontos nº ' . $this->exchange->customers_id, $url)
                           ->fields([
                                'ID do Usuário' => $this->exchange->customers_id,
                                'Nome' => $this->exchange->customer->fullname,
                                'CPF' => $this->applyMask($this->exchange->customer->cpf, '###.###.###-##'),
                                'Nascimento' => $this->parseBrBirthdate($this->exchange->customer->birthdate),
                                'E-mail' => $this->exchange->customer->email,
                                'Produto' => $this->exchange->product_service->title,
                                'ID do Produto' => $this->exchange->product_service->id,
                                'Pontos' => $this->exchange->product_service->points,
                            ]);
        });
    }

    private function applyMask($val, $mask)
    {
        $maskared = '';
        $k = 0;

        for($i = 0; $i<=strlen($mask)-1; $i++)
        {
            if($mask[$i] == '#')
            {
                if(isset($val[$k]))
                $maskared .= $val[$k++];
            }

            else
            {
                if(isset($mask[$i]))
                $maskared .= $mask[$i];
            }
        }

        return $maskared;
    }

    private function parseBrBirthdate($birthdate)
    {
        $year  = substr($birthdate, 0, 4);
        $month = substr($birthdate, 5, 2);
        $day   = substr($birthdate, 8, 2);

        return ($day . '/' . $month . '/' . $year);
    }

}