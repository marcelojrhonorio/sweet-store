<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Services\CustomerService;
use App\Services\SsiSweetService;
use App\Notifications\SsiPostback;
use Illuminate\Support\Facades\Log;
use App\Services\HasOffersPixelService;
use App\Models\Notifications\SsiPostbackNotification;

class SsiController extends Controller
{
    private static $pixels = [
        "complete" => "http://sweet.go2cloud.org/aff_lsr?offer_id=183&aff_id=1016",
        "quotafull" => "http://sweet.go2cloud.org/aff_goal?a=lsr&goal_id=17&aff_id=1016",
        "screenout" => "http://sweet.go2cloud.org/aff_goal?a=lsr&goal_id=16&aff_id=1016",
        "noprojects" => "http://sweet.go2cloud.org/aff_goal?a=lsr&goal_id=18&aff_id=1016",
    ];

    //
    public function postback(Request $request, string $postback)
    {

        $fullUrl = $request->fullUrl();
        $params  = self::getParams($request);
        $points  = self::getPoints($postback);
               
        /**
         * Send Slack Notification
         */
        $ssiPostbackNotification = new SsiPostbackNotification();
        $ssiPostbackNotification->notify(new SsiPostback($params, $postback, $fullUrl));

        // disparar o pixel
        // pesquisar o usuário
        $c = self::getCustomer((int) $params['sourcePID']);
        $previousBalance = $c->points;
        
        if ($c) {
            $updatedSurvey = SsiSweetService::updateSurvey($points, $params);

            if ($updatedSurvey) {
                self::updateCustomerPoints($c, $points);
            }
        }

        // HasOffersPixelService::dispacth(self::getPixel($postback));

        CustomerService::renewToken($c);

        CustomerService::setUserSession($c);
        // dd($postback, 1);

        if(('' == $params['sourceData'])) {
            $points = 0;
        }

        if((null == $updatedSurvey) && 'noprojects' !== $postback) {
            $points = 0;
        }

        return self::redirect($c, $points, $previousBalance);
    }

    private static function updateCustomerPoints(Customer $c = null, int $points = 0)
    {
        if ($c) {
            $c->points += $points;
            $c->update();
        }
    }

    private static function redirect($c, $points, $previousBalance)
    {

        return redirect('/researches/resume')
            ->with(
                'comingFromResearch',
                [
                    'earnedPoints' => $points,
                    'previousBalance' => $previousBalance,
                    'researchPoints' => $points,
                ]
            );
    }
    private static function getCustomer(int $customer_id = 0): Customer
    {
        if ($customer_id) {
            return Customer::find($customer_id);
        }
        return null;
    }

    private static function getParams(Request $request = null): array
    {
        if (null === $request) {
            return [];
        }
        $default = '';
        return [
            'sourceData' => $request->query('sourceData', $default),
            'sourcePID' => $request->query('sourcePID', $default),
            'projectID' => $request->query('projectID', $default),
            'bidLength' => $request->query('bidLength', $default),
            'projectType' => $request->query('projectType', $default),
            'hashparam' => $request->query('hashparam', $default),
        ];
    }

    private static function getPoints(string $research_type = ''): int
    {
        if ('' === $research_type || 'noprojects' === $research_type ) {
            return 0;
        }
        if ('complete' === $research_type) {
            return 60;
        }
        return 10;
    }

    private static function getPixel(string $research_type = '')
    {
        if ('' === $research_type) {
            return self::$pixels['noprojects'];
        }
        return self::$pixels[$research_type];
    }

    public function showSurvey ($email, Request $request) 
    {
        $customer     = Customer::where('email', $email)->first();
        $researchLink = $request->query('site') . '&sourceData=' . $request->query('sourceData');

        if (null === $customer) {
            return redirect($researchLink);
        }

        $this->setCurrentUser($customer);

        return redirect('/researches')->with(
            'comingFromEmail', ['researchLink' => $researchLink]
        );
    }

    private function setCurrentUser($customer)
    {
        session([
            'id'        => $customer->id,
            'name'      => $customer->fullname,
            'email'     => $customer->email,
            'birthdate' => $customer->birthdate,
            'gender'    => $customer->gender,
            'cep'       => $customer->cep,
            'avatar'    => $customer->avatar,
            'points'    => $customer->points,
            'token'     => $customer->token,
            'confirmed' => $customer->confirmed,
        ]);
    }
}
