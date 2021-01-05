<?php

namespace App\Http\Controllers\Exchange;

use Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\SweetStaticApiTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;

class ExchangePointsSmController extends Controller
{
    use SweetStaticApiTrait;

    public function store(Request $request)
    {
        $data = [
            'products_services_id'  =>  $request->input('products_services_id'),
            'social_media'          =>  $request->input('social_media'),
            'customers_id'          =>  $request->input('customers_id'),
            'subject'               =>  $request->input('subject'),
            'profile_link'          =>  $request->input('profile_link'),
            'points'                =>  $request->input('points'),
            'profile_picture'       =>  $request->input('profile_picture'),
            'status'                =>  'pending',
        ];

        //não modificar imagem por ter nomes iguais.
        if (session()->has('imageNamePS')) {
            session()->put('imageNamePS', null);
        } 

        try {

            $response = self::executeSweetApi(
                'POST',
                'api/exchange/v1/frontend/exchanged-points-sm',
                $data
            );

            session()->put('points', $response->customer->points);

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
        } catch (BadResponseException $exception) {
            Log::debug($exception->getMessage());
        }   
    }

    public function verifyLink(Request $request)
    {
        try {

            $response = self::executeSweetApi(
                'POST',
                'api/exchange/v1/frontend/exchanged-points-sm/verify-link',
                [
                    'profile_link' => $request->input('profile_link'),
                ]
            );

            if($response->success) {
                return response()->json([
                    'success' => true,
                    'data'    => $response,
                ]);
            } 
            
            return response()->json([
                'success' => false,
                'data'    => $response,
            ]);
            

        } catch (RequestException $exception) {
            Log::debug($exception->getMessage());
        } catch (ConnectException $exception) {
            Log::debug($exception->getMessage());
        } catch (ClientException $exception) {
            Log::debug($exception->getMessage());
        } catch (BadResponseException $exception) {
            Log::debug($exception->getMessage());
        }  

    }

    public function uploadImage(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
        ]);

        $imageUpload = $request->file('image');

        if (session()->has('imageNamePS') && (null != session()->get('imageNamePS'))) {
            $imageName = session()->get('imageNamePS');
        } else {
            $imageName = uniqid(time()) . '.' . $imageUpload->getClientOriginalExtension();
        } 

        $date            = Carbon::now();
        $path            = 'bonus/sm-exchange/images/'. $date->year . '/' . $date->month . '/';
        $destinationPath = storage_path('app/public/' . $path);

        File::makeDirectory($destinationPath, 0777, true, true);

        $image = \Image::make($imageUpload->getRealPath());
        $image->resize(210, 175);
        $image->save($destinationPath . $imageName);

        session()->put('imagePS', $path.$imageName);
        session()->put('imagePathPS', $path);
        session()->put('imageNamePS', $imageName);

        $data = [
            'path' => $path,
            'name' => $imageName,
        ];

        return response()->json([
            'success' => true,
            'data'    => $data,
        ], 201);
    }
}
