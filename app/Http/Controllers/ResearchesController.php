<?php

namespace App\Http\Controllers;

use App\Contracts\Research\WithCustomerPostback;
use App\Contracts\Research\WithoutCustomerPostback;
use App\Models\Research;
use App\Models\ResearchPixel;
use App\Models\RaptorResearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResearchesController extends Controller
{

    public function resume(Request $request)
    {
        if (empty(session('comingFromResearch'))) {
            return redirect('/');
        }

        $comingFromResearch = session('comingFromResearch');

        return view('researches.resume')->with('comingFromResearch', $comingFromResearch);
    }

    public function postback(Request $request)
    {
        $default = 'Não informado';

        $params = [
            'customer_id' => $request->query('customer_id', $default),
            'hasoffers_id' => $request->query('hasoffers_id', $default),
            'affiliate_id' => $request->query('affiliate_id', $default),
            'research_type' => $request->query('research_type', $default),
            'transaction_id' => $request->query('transaction_id', $default),
            'confirmed' => true
        ];

        if($params['hasoffers_id'] == 220) {
            Log::debug('raptor research postback');
            Log::debug($params);
            $raptorResearch = new RaptorResearch();
            $raptorResearch->customer_id = $params['customer_id'];
            $raptorResearch->hasoffers_id = $params['hasoffers_id'];
            $raptorResearch->affiliate_id = $params['affiliate_id'];
            $raptorResearch->research_type = $params['research_type'];
            $raptorResearch->transaction_id = $params['transaction_id'];
            $raptorResearch->confirmed = $params['confirmed'];
            $raptorResearch->save();
            return redirect('/');
        }

        if (strpos($params['customer_id'], 'f') !== false || strpos($params['customer_id'], 'F')  !== false) {
            $params['customer_id'] = (int) explode("-", $params['customer_id'])[1];
            $params['confirmed'] = false;
        }

        $research = Research::where('hasoffers_id', $params['hasoffers_id'])->first();

        if (empty($research)) {

            Log::debug('Não existe pesquisa cadastrada para o hasoffers_id ' . $params['hasoffers_id']);

            return redirect('/');
        }

        $pixel = ResearchPixel::where('research_id', $research->id)
            ->where('type', (int) $params['research_type'])
            ->where('affiliate_id', (int) $params['affiliate_id'])
            ->first();

        if (empty($pixel)) {

            Log::debug('Não existe pixel cadastrado para a pesquisa ' . $research->id .
                ', affiliate_id ' . $params['affiliate_id'] . ' e tipo de pesquisa ' . $params['research_type']);

            return redirect('/');

        }

        if ($pixel->has_redirect) {

            $strategyPostback = new WithoutCustomerPostback($params);

        } else {

            $strategyPostback = new WithCustomerPostback($params);

        }

        $url = $strategyPostback->runPixel();

        return redirect('/researches/resume')
            ->with('comingFromResearch',
                [
                    'earnedPoints' => '1' === $params['research_type'] ? $url['researchPoints'] : 0,
                    'previousBalance' => $url['previousBalance'] ?? 0,
                    'researchPoints' => $research->points,
                ]
            );

    }
}
