<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Events\CustomerVerifiedEvent;
use Illuminate\Support\Facades\Response;

class DoubleOptinPixelController extends Controller
{
    //
    public function handle(Request $request)
    {
        $email = $request->query("email");
        $c = Customer::where("email",$email)->first();
        
        if (isset($c->confirmed) && !$c->confirmed) {

            self::doCheckin($c->id);

            $verified = new CustomerVerifiedEvent($c);
            event($verified);
        }

        return self::returnPixel();
    }

    private static function returnPixel()
    {
        $image="\x47\x49\x46\x38\x37\x61\x1\x0\x1\x0\x80\x0\x0\xfc\x6a\x6c\x0\x0\x0\x2c\x0\x0\x0\x0\x1\x0\x1\x0\x0\x2\x2\x44\x1\x0\x3b";
        return \response($image,200)->header('Content-Type', 'image/gif');;
    }

    private static function doCheckin (int $id)
    {
        $customer = Customer::find($id);

        if (isset($customer->confirmed) && !$customer->confirmed) {
            $customer->confirmed = 1;
            $customer->points += 30;
            $customer->confirmed_at = Carbon::now()->toDateTimeString();
            $customer->save();
        }

        session(['points' => $customer->points]);        
    }

}
