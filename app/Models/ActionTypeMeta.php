<?php
/**
 * @todo Add docs.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @todo Add docs.
 */
class ActionTypeMeta extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array $fillable
     */
    protected $fillable = [
        'action_id',
        'action_type_id',
        'key',
        'value',
    ];

    /**
     * Get the action for the meta.
     */
    public function action()
    {
        return $this->belongsTo('App\Models\Action');
    }

    /**
     * Get the type for the meta.
     */
    public function actionType()
    {
        return $this->belongsTo('App\Models\ActionType');
    }
}
