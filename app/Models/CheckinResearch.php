<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckinResearch extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customers_id',
        'researches_id',
    ];

    /**
     * Get the research for the checkin.
     */
    public function research()
    {
        return $this->belongsTo('App\Models\Research');
    }
}
