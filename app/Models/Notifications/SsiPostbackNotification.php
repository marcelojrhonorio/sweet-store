<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SsiPostbackNotification extends Model
{
    use Notifiable;

    public function routeNotificationForSlack() {
        return 'https://hooks.slack.com/services/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';
    } 
}
