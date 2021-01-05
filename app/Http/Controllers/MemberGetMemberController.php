<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Jobs\MemberGetMemberJob;
use App\Traits\SweetStaticApiTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;

class MemberGetMemberController extends Controller
{
    use SweetStaticApiTrait;    

    public function index()
    {        
        if (empty(session('comingFromSocialActions'))) {
            return redirect('/');
        }

        $comingFromSocialActions = session('comingFromSocialActions');

        $domain = preg_match("/(?:uat-store)/", URL::current()) ? 'uat-store' : 'store';
        
        if(!session()->has('share_indicated_by') && session('comingFromSocialActions')) {
            $this->setCurrentData($comingFromSocialActions, 'share-action', $domain); 
        }
       
        return view('login')->with([
            'data'      => $comingFromSocialActions,
            'page'      => 'share-action',
            'domain'    => $domain,
        ]);

    }

    protected function setCurrentData($comingFromSocialActions, $page, $domain)
    {
        session([
            'share_indicated_by'  => $comingFromSocialActions['customer_id'],
            'share_name_indicated_by'  => $comingFromSocialActions['customer_name'],
            'share_action_id'  => $comingFromSocialActions['action_id'],
            'share_action_type'  => $comingFromSocialActions['action_type'],
            'share_page'  => $page,
            'share_domain'  => $domain,
        ]);
    }

    public function sendEmail(Request $request, $id){
        $customer = Customer::find($id);

        if($customer->clicks_share_mail < 3){
            
            MemberGetMemberJob::dispatch($customer)->onQueue('store_member_get_member');
    
            $customer->clicks_share_mail += 1;
            $customer->save();

        }

        session([
            'clicks_share_mail' => $customer->clicks_share_mail,
        ]);

        return $customer->clicks_share_mail;
    }

}
