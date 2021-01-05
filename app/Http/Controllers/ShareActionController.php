<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ShareActionController extends Controller
{
    public function postback(Request $request)
    {        
        $default = 'Não informado'; 

        $params = [
            'customer_id'  => $request->query('customer_id', $default),
            'action_id'    => $request->query('action_id', $default),
            'action_type'  => $request->query('action_type', $default),           
        ];

        $customer = Customer::find($params['customer_id']);
        $customerName = explode(" ", $customer->fullname)[0];

        $session = $request->session();
       
        //if logged, redirect to url action
        if($session->has('id')){
            return redirect('/share-action/login?utm_source=ShareFacebook&utm_campaign=MemberGetMember')
                ->with('comingFromSocialActions', [
                    'customer_id'   => $params['customer_id'],
                    'customer_name' => $customerName,
                    'action_id'     => $params['action_id'],
                    'action_type'   => $params['action_type'],
                ]);
        }       
        
        return redirect('/share-action?utm_source=ShareFacebook&utm_campaign=MemberGetMember')
            ->with('comingFromSocialActions', [
                'customer_id'   => $params['customer_id'],
                'customer_name' => $customerName,
                'action_id'     => $params['action_id'],
                'action_type'   => $params['action_type'],
            ]);

    }

    public function postbackShare(Request $request)
    {
        $default = 'Não informado';        

        $params = [
            'customer_id'  => $request->query('customer_id', $default),
            'action_id'    => $request->query('action_id', $default),
            'action_type'  => $request->query('action_type', $default),           
        ];
        
        $customer = Customer::find($params['customer_id']);
        $customerName = explode(" ", $customer->fullname)[0];

        return redirect('/share-action/login?utm_source=ShareFacebook&utm_campaign=MemberGetMember')
            ->with('comingFromSocialActions', [
                'customer_id'   => $params['customer_id'],
                'customer_name' => $customerName,
                'action_id'     => $params['action_id'],
                'action_type'   => $params['action_type'],
            ]);

    }

    
}
