<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Traits\SweetStaticApiTrait;

class VipListSubscribersController extends Controller
{
    use SweetStaticApiTrait;

    public function create(Request $request) 
    {
        $data = [
            'phone' => $request->input('phone'),
            'name' => $request->input('name'),
        ];

        try {

            $response = self::executeSweetApi(
                'POST',
                '/api/v1/frontend/vip-list/create',
                $data
            );

            return response()->json([
                'success' => true,
                'data'    => $response,
            ]);
            
        } catch (RequestException $exception) {
            Log::debug($exception->getMessage());
        } catch (ConnectException $exception) {
            Log::debug($exception->getMessage());
        } catch (ClientException $exception) {
            Log::debug($exception->getMessage());
        }
    }

    public function verify(Request $request) 
    {
        $phone = $request->input('phone');

        try {

            $response = self::executeSweetApi(
                'POST',
                'api/v1/frontend/vip-list/verify-phone',
                [
                    'phone' => $phone
                ]
            );

            if($response) {
                return response()->json([
                    'success' => true,
                    'data'    => $response,
                ]);
            }

            return response()->json([
                'success' => false,
                'data'    => [],
            ]);
            
        } catch (RequestException $exception) {
            Log::debug($exception->getMessage());
        } catch (ConnectException $exception) {
            Log::debug($exception->getMessage());
        } catch (ClientException $exception) {
            Log::debug($exception->getMessage());
        }
    }

    public function update(Request $request)
    {
        $phone = $request->input('phone');
        $name = $request->input('name');
        $older_phone = $request->input('older_p');

        try {

            $response = self::executeSweetApi(
                'POST',
                '/api/v1/frontend/vip-list/update',
                [
                    'phone' => $phone,
                    'name' => $name,
                    'older_phone' => $older_phone,
                ]
            );

            return response()->json([
                'success' => true,
                'data'    => $response,
            ]);
            
        } catch (RequestException $exception) {
            Log::debug($exception->getMessage());
        } catch (ConnectException $exception) {
            Log::debug($exception->getMessage());
        } catch (ClientException $exception) {
            Log::debug($exception->getMessage());
        }
    }
}
