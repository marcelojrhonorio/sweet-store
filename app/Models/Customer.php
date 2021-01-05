<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    use Notifiable;

    /**
     * The attributes that aren't mass assignable.
     */
    protected $guarded = [
        'id',
        'points',
        'confirmed',
        'resend_attempts',
        'created_at',
        'update_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get the campaign answers for the customer.
     */
    public function answers()
    {
        return $this->hasMany('App\Models\CampaignAnswers', 'customers_id', 'id');
    }

    /**
     * Get the checkins for the customer.
     */
    public function checkins()
    {
        return $this->hasMany('App\Models\Checkin');
    }

    public function routeNotificationForSlack() {
        return 'https://hooks.slack.com/services/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';
    }    
}
