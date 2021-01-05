<?php

namespace App\Http\Controllers\IncentiveEmails;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Events\CustomerCheckoutEvent;
use App\Models\IncentiveEmails\IncentiveEmail;
use App\Http\Controllers\IncentiveEmails\EarnPointsController;
use App\Http\Controllers\IncentiveEmails\CreateCheckinController;
use App\Http\Controllers\IncentiveEmails\ParamsValidatorController;

class CheckinIncentiveEmailsController extends Controller
{
    public function postback(Request $request)
    {
        $default = 0;
        $defaultEmail = 'email@nao.passado';

        $params = [
            'incentive_email_id'    => $request->query('incentive_email_id',      $default),
            'incentive_email_code'  => $request->query('incentive_email_code',    $default),
            'customers_id'          => $request->query('customers_id',            $default),
            'customers_email'       => $request->query('customers_email',    $defaultEmail),
        ];

        $verify = ParamsValidatorController::verify($params);
        
        $incentiveEmail = (($default === $params['incentive_email_code']) ? DB::table('incentive_emails')->where('id', $params['incentive_email_id'])->first() : DB::table('incentive_emails')->where('code', $params['incentive_email_code'])->first());
        
        if(!$verify){
            return redirect($incentiveEmail->redirect_link ?? (env('SWEETBONUS_URL')));
        }

        $idCustomer = $params['customers_id'];

        if($default === $idCustomer)
        {
            $idCustomer = self::getIdCustomer($params['customers_email']);
        }
        
        $checkout = new CustomerCheckoutEvent($idCustomer, 'incentive_email', $incentiveEmail->id);
        event($checkout);

        CreateCheckinController::checkin($params);

        return redirect($incentiveEmail->redirect_link);
    }


    public static function getIdCustomer($email)
    {
        $id = DB::table('customers')
                ->where('email', $email)
                ->select('id')
                ->get();

        return (int) $id[0]->id;
    }

   
}
