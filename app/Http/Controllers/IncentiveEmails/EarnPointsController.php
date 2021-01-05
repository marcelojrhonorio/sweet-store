<?php

namespace App\Http\Controllers\IncentiveEmails;

use DB;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\IncentiveEmails\IncentiveEmail;

class EarnPointsController extends Controller
{
    public static function earn($data)
    {
        $incentiveEmail = (0 === $data['incentive_email_code'] ? DB::table('incentive_emails')->where('id', $data['incentive_email_id'])->first() : DB::table('incentive_emails')->where('code', $data['incentive_email_code'])->first());
        $customer =('email@nao.passado'===$data['customers_email'] ? Customer::find($data['customers_id']) : Customer::where('email',$data['customers_email'])->first());
        
        $customer->points +=  $incentiveEmail->points;
        $customer->save();

        return $customer;
    }
}
