<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CustomerCheckoutEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $customerId, $actionType, $actionId;

    /**
     * Create a new event instance.
     */
    public function __construct($customerId, $actionType, $actionId)
    {
        $this->customerId = $customerId;
        $this->actionType = $actionType;
        $this->actionId   = $actionId;
    }
}
