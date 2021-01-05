<?php

namespace App\Http\Controllers\IncentiveEmails;

use DB;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\IncentiveEmails\IncentiveEmail;
use App\Models\IncentiveEmails\CheckinIncentiveEmail;

class ParamsValidatorController extends Controller
{
    public static function verify($data)
    {
        $customer =('email@nao.passado'===$data['customers_email'] ? Customer::find($data['customers_id']) : Customer::where('email',$data['customers_email'])->first());

        if(empty($customer)){
            Log::debug('Usuário ' . $data['customers_id'] .' não encontrado');
            return false;
        }

        $incentiveEmail = (0 === $data['incentive_email_code'] ? DB::table('incentive_emails')->where('id', $data['incentive_email_id'])->first() : DB::table('incentive_emails')->where('code', $data['incentive_email_code'])->first());

        if(empty($incentiveEmail)){
            Log::debug('E-mail incentivado com id ' . $data['incentive_email_id'] .' e código ' . $data['incentive_email_code'] . ' não encontrado');
            return false;
        }

        if((null !== $incentiveEmail->code) && (0 !== $data['incentive_email_id']))
        {
            Log::debug('Foi passado o ID do e-mail incentivado, mas o parâmetro válido é o código. Código do e-mail incentivado: ' . $incentiveEmail->code);
            return false;
        }

        $alreadyTaken =
            CheckinIncentiveEmail::
                where('incentive_emails_id',  $incentiveEmail->id)
                ->where('customers_id', $customer->id)
                ->first();

        if(!empty($alreadyTaken))
        {
            Log::debug('O usuário ' . $customer->id . ' já realizou o e-mail incentivado de id ' . $incentiveEmail->id);
            return false;
        }

        return true;
    }
}
