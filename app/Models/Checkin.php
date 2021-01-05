<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customers_id',
        'actions_id',
    ];

    /**
     * Get the customer for the checkin.
     */
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }
}
