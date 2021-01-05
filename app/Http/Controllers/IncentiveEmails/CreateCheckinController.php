<?php

namespace App\Http\Controllers\IncentiveEmails;

use DB;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\IncentiveEmails\IncentiveEmail;
use App\Models\IncentiveEmails\CheckinIncentiveEmail;

class CreateCheckinController extends Controller
{
    public static function checkin($data)
    {
        $incentiveEmail = ((0 === $data['incentive_email_code']) ? DB::table('incentive_emails')->where('id', $data['incentive_email_id'])->first() : DB::table('incentive_emails')->where('code', $data['incentive_email_code'])->first());
        
        $customer =('email@nao.passado'===$data['customers_email'] ? DB::table('customers')->where('id', $data['customers_id'])->first() : DB::table('customers')->where('email', $data['customers_email'])->first());
        
        $checkinIncentiveEmail = new CheckinIncentiveEmail();
        $checkinIncentiveEmail->incentive_emails_id = $incentiveEmail->id;
        $checkinIncentiveEmail->customers_id        = $customer->id;
        $checkinIncentiveEmail->points              = $incentiveEmail->points;
        $checkinIncentiveEmail->save();

        return $checkinIncentiveEmail;
    }
}
