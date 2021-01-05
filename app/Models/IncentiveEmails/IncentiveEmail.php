<?php

namespace App\Models\IncentiveEmails;

use Illuminate\Database\Eloquent\Model;

class IncentiveEmail extends Model
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
