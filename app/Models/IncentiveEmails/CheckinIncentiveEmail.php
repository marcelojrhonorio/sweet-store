<?php

namespace App\Models\IncentiveEmails;

use Illuminate\Database\Eloquent\Model;

class CheckinIncentiveEmail extends Model
{
    /**
     * The attributes that aren't mass assignable.
     */
    protected $guarded = [
        'id',
        'created_at',
        'update_at',
    ];
}
