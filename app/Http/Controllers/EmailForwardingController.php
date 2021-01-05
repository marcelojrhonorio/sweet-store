<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Traits\SweetStaticApiTrait;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Jobs\SendEmailForForwardingJob;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;

class EmailForwardingController extends Controller
{
    use SweetStaticApiTrait;

    public function store(Request $request)
    {
        $customers_id = session('id');
        $names = $request->input('names');
        $emails = $request->input('emails');
        $prints = $request->input('prints');

        //verificar se já foi encaminhado para o e-mail informado
        for ($i=0; $i < count($emails); $i++)
        {
            $verify = self::verifyEmail($emails[$i]); 

            //retorna erro para view
            if(!$verify['success']) {
                return response()->json([
                    'success' => false,
                    'data' => $verify['email'],
                ]);
            }
        }

        //create email_forwarding
        $email_forwarding = self::createEmailForwarding();

        //create customers_forwarding
        $customers_forwarding = self::createCustomersForwarding($customers_id, $email_forwarding->id);

        //create customers_forwarding_emails
        $customers_forwarding_emails = self::createCustomersForwardingEmails($customers_forwarding->id, $names, $emails);        

        //create customers_forwarding_prints
        $customers_forwarding_prints = self::createCustomersForwardingPrints($customers_forwarding->id, $prints);

        //verify and create (or update) customers_forwarding_status
        $customers_forwarding_status = self::createCustomersForwardingStatus($customers_id);
        
        return response()->json([
            'success' => true,
            'data' => $customers_forwarding,
        ]);
    } 

    public function uploadImage(Request $request)
    {        
        $this->validate($request, [
            'image.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
        ]);
            
        $uploadedImages = [];
        $imageUploads = $request->file('image');

        foreach ($imageUploads as $imageUpload) 
        {
            $imageName = uniqid(time()) . '.' . $imageUpload->getClientOriginalExtension();

            $date            = Carbon::now();
            $path            = 'bonus/email-forwarding/images/'. $date->year . '/' . $date->month . '/';
            $destinationPath = storage_path('app/public/' . $path);
    
            File::makeDirectory($destinationPath, 0777, true, true);
    
            $image = \Image::make($imageUpload->getRealPath());
            $image->save($destinationPath . $imageName);
    
            $data = [
                'path' => $path,
                'name' => $imageName,
            ];

            array_push($uploadedImages, $data);
        }

        return response()->json([
            'success' => true,
            'data'    => $uploadedImages,
        ], 201);
    }

    public function sendEmail(Request $request)
    {
        $customers_id = session('id');

        $result = self::verifyForwardingEmailSent($customers_id);

        if($result >= 10) {
            return response()->json([
                'success' => false,
                'data'    => $result,
            ], 201);
        }

        $forwarding_email_sent = self::updateForwardingEmailSent($customers_id); 

        $email = session('email');
        $name = explode(" ", session('name'))[0];

        SendEmailForForwardingJob::dispatch($customers_id, $name, $email)->onQueue('send_email_forwarding');

        return response()->json([
            'success' => true,
            'data'    => (10 - $forwarding_email_sent),
        ], 201);
    }

    private static function updateForwardingEmailSent($customers_id)
    {
        try {
            $response = self::executeSweetApi(
                'PUT',
                '/api/v1/frontend/customers/update-customer/forwarding-email-sent',
                [
                    'customers_id' => $customers_id
                ]
            );

            return $response;

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

    private function createCustomersForwardingStatus($customers_id)
    {
        try {
            $response = self::executeSweetApi(
                'POST',
                '/api/email-forwarding/v1/frontend/customers-forwarding-status/create',
                [
                    'customers_id' => $customers_id,
                    'email'        => session('email'),
                    'name'         => session('name')
                ]
            );

            return $response;

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

    private static function verifyForwardingEmailSent($customers_id)
    {
        try {
            $response = self::executeSweetApi(
                'POST',
                '/api/v1/frontend/customers/update-customer/verify-forwarding-email',
                [
                    'customers_id' => $customers_id
                ]
            );

            return $response;

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

    private static function createCustomersForwardingPrints($customers_forwarding_id, $prints)
    {
        $images = explode(",", $prints);
        
        foreach ($images as $image) 
        {            
            try {
           
                if('' !== $image) 
                {
                    $response = self::executeSweetApi(
                        'POST',
                        '/api/email-forwarding/v1/frontend/customers-forwarding-print/create',
                        [
                            'customers_forwarding_id' => $customers_forwarding_id,
                            'image' => $image
                        ]
                    );
                }                
               
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
         
        return;
    }

    private static function createCustomersForwardingEmails($customers_forwarding_id, $names, $emails)
    { 
        for ($i=0; $i < count($names); $i++)
        {  
            try {
           
                $response = self::executeSweetApi(
                    'POST',
                    '/api/email-forwarding/v1/frontend/customers-forwarding-email/create',
                    [
                        'customers_forwarding_id' => $customers_forwarding_id,
                        'name' => $names[$i],
                        'email' => $emails[$i]
                    ]
                );
               
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
        
        return;
    }

    private static function verifyEmail($email)
    {
        try {
           
            $response = self::executeSweetApi(
                'GET',
                '/api/email-forwarding/v1/frontend/customers-forwarding-email?where[email]='.$email,
                []
            );
    
            if($response->data) {
                return [
                    'success' => false,
                    'email' => $email,
                ];
            }

            return [
                'success' => true,
                'email' => [],
            ];
           
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
    
    private static function createCustomersForwarding($customers_id, $email_forwarding_id)
    {
        try {
           
            $response = self::executeSweetApi(
                'POST',
                '/api/email-forwarding/v1/frontend/customers-forwarding/create',
                [
                    'customers_id' => $customers_id,
                    'email_forwarding_id' => $email_forwarding_id
                ]
            );
    
            return $response;
           
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

    private static function createEmailForwarding()
    {
        try {
           
            $response = self::executeSweetApi(
                'POST',
                '/api/email-forwarding/v1/frontend/email-forwarding/create',
                []
            );
    
            return $response;
           
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
   
}
