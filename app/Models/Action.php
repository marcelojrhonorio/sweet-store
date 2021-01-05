<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @todo Add docs.
 */
class Action extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array $fillable
     */
    protected $fillable = [
        'title',
        'path_image',
        'description',
        'grant_points',
        'action_category_id',
        'action_type_id',
    ];

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = [
        'deleted_at',
    ];

    /**
     * Get the checkins for the action.
     */
    public function checkins()
    {
        return $this->hasMany('App\Models\Checkin');
    }
}
