<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CarInsuranceCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $customer_id;

    /**
     * Create a new event instance.
     */
    public function __construct($customer_id)
    {
        $this->customer_id = $customer_id;
    }
}
