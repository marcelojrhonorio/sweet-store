<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FeaturedActionsController extends Controller
{
    use InteractsWithQueue, Queueable, SerializesModels;
    // TODO: Removed the Dispatchable, Verify with the Henrique what's the problem with this trait!
    // use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $customer;

    private $endpoint;

    public function __construct($customer)
    {
        $this->customer = $customer;
        $this->endpoint = 'https://transacional.allin.com.br/api';
    }

    private function renewToken()
    {
        $client = new Client(['base_uri' => $this->endpoint]);

        $params = [
            'method'   => 'get_token',
            'output'   => 'json',
            'username' => env('ALLIN_USER'),
            'password' => env('ALLIN_PASS'),
        ];

        $query = urldecode(http_build_query($params));

        $response = $client->get('?' . $query);

        $json = json_decode($response->getBody()->getContents());

        return $json->token;
    }

    public function index()
    {
        $customerId = 2;

        $mostClicked = DB::table('checkins')
                        ->selectRaw('actions_id, COUNT(*) AS clicks')
                        ->where('customers_id', '!=', $customerId)
                        ->groupBy('actions_id')
                        ->orderBy('clicks', 'DESC')
                        ->take(2)
                        ->get();

        $mostClickedIds = $mostClicked->pluck('actions_id')->toArray();

        $customerClicked = DB::table('checkins')
                            ->selectRaw('actions_id')
                            ->where('customers_id', $customerId)
                            ->get();

        $customerClickedIds = $customerClicked->pluck('actions_id')->toArray();

        $actionsToExclude = array_merge($mostClickedIds, $customerClickedIds);

        $greatPoints = DB::table('actions')
                        ->selectRaw('id')
                        ->whereNotIn('id', $actionsToExclude)
                        ->orderBy('grant_points', 'DESC')
                        ->take(2)
                        ->get();

        $oportunities = DB::table('actions')
                        ->selectRaw('id')
                        ->where('grant_points', 0)
                        ->whereNotIn('id', $actionsToExclude)
                        ->orderBy('created_at', 'ASC')
                        ->take(2)
                        ->get();

        $response = [
            'mostClicked'  => $mostClicked,
            'greatPoints'  => $greatPoints,
            'oportunities' => $oportunities,
        ];

        return response()->json($response);
    }
}
