<?php

namespace App\Services;

use App\Events\CustomerVerifiedEvent;
use App\Models\Customer;

class ResearchDoubleOptinService
{

    private $params;

    const AFFILIATES = [
        '1016',
        '1003',
    ];

    public function __construct($params)
    {

        $this->params = $params;
        $this->customer = Customer::find($params['customer_id']);

    }

    public function confirm()
    {

        if ($this->wasConfirmed()) {
            return;
        }

        if (in_array($this->params['affiliate_id'], self::AFFILIATES)) {

            $this->customer->confirmed = 1;
            $this->customer->confirmation_code = null;

            $this->customer->save();

            $verified = new CustomerVerifiedEvent($this->customer);

            event($verified);

            $this->doCheckin();
        }

    }

    private function wasConfirmed()
    {
        return $this->customer->confirmed ?: false;
    }

    private function doCheckin()
    {

        $this->customer->points += 30;

        $this->customer->save();

    }

}
