<?php

namespace App\Http\Controllers\SocialClass;

use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use GuzzleHttp\Stream\Stream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Traits\SweetStaticApiTrait;
use App\Http\Controllers\Controller;
use App\Events\CustomerCheckoutEvent;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;

class SocialClassController extends Controller
{
    use SweetStaticApiTrait;

    public function search (Request $request)
    {
        return response()->json([
            'success' => true,
            'status'  => 'Sucesso!',
            'data'    => self::getResearch(),
        ]);        
    }

    public function store (Request $request) {
        $data = $request->all();

        /**
         * Get full question and relationships.
         */
        $question = self::getQuestion($data['answer']);

        /**
         * Store answer in API.
         */
        $storedAnswer = self::storeAnswerAPI($question->research_question->id, $question->id);

        /**
         * If an error occurred.
         */
        if (!isset($storedAnswer->customers_id)) {
            return response()->json([
                'success' => false,
                'status' => 'unknown_error',
                'data' => [],
            ]);
        }

        /**
         * Update questions points.
         */
        self::updatePoints(session()->get('id'), $question->points);

        return response()->json([
            'success' => true,
            'status' => 'Sucesso!',
            'data' => [
                'answer' => $data['answer'],
                'customers_id' => $request->session()->get('id'),
                'api_response' => $question,
            ],
        ]);
    }

    private static function getResearch () {
        try {
            $response = self::executeSweetApi(
                'GET',
                '/api/social-class/v1/frontend/research-question',
                []
            );

            return self::notAnswered($response);

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

    private static function getQuestion($optionId)
    {
        try{

            $response = self::executeSweetApi(
                'GET',
                '/api/social-class/v1/frontend/research-option/' . $optionId,
                []
            );

            return $response ?? null;

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

    private static function storeAnswerAPI(int $questionId, int $optionId) {
        try {

            $data = [
                'customers_id' => session()->get('id'),
                'question_id' => $questionId,
                'option_id' => $optionId
            ];
    
            $response = self::executeSweetApi(
                'POST',
                '/api/social-class/v1/frontend/research-answer',
                $data
            );
    
            return $response->data ?? null;

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

    private static function updatePoints(int $customerId, int $points)
    {
        try {
            $getFsc = self::executeSweetApi(
                'GET',
                '/api/social-class/v1/frontend/final?where[customers_id]='.$customerId,
                []
            );

            $getFsc = $getFsc->data[0] ?? null;

            /**
             * Create a final_social_class record if is no created.
             */
            if (null === $getFsc) {
                $creataFsc = self::executeSweetApi(
                    'POST',
                    '/api/social-class/v1/frontend/final',
                    [
                        'customers_id' => $customerId,
                        'final_points' => $points, 
                    ]
                );
                
            /**
             * If already created, update record. 
             */
            } else {
                $updateFsc = self::executeSweetApi(
                    'PUT',
                    '/api/social-class/v1/frontend/final/'.$getFsc->id,
                    [
                        'customers_id' => $getFsc->customers_id,
                        'final_points' => (int) $getFsc->final_points + (int) $points, 
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

    public function checkout (Request $request)
    {
        self::generateFinalSocialClass();

        /**
         * Update points in customers table.
         */
        $finalPoints = self::updateCustomerPoints();

        /**
         * Won Stamp.
         */
        $checkout = new CustomerCheckoutEvent(session()->get('id'), 'profile', 0);
        event($checkout);

        /**
         * Update session.
         */
        $currentPoints = $request->session()->get('points');
        $request->session()->forget('points');
        $request->session()->put('points', $currentPoints + 60);

        return response()->json([
            'success' => true,
            'status' => 'Sucesso!',
            'data' => [
                'points' => $finalPoints,
            ],
        ]);        
    }

    private static function generateFinalSocialClass() {
        try {
            $final = self::executeSweetApi(
                'GET',
                '/api/social-class/v1/frontend/final?where[customers_id]='.session()->get('id'),
                []
            );

            $points = $final->data[0]->final_points;

            /**
             * Final social class.
             */
            if ($points >= 1 && $points <= 16) {
                $socialClass = 'D-E';
            } elseif ($points >= 17 && $points <= 22) {
                $socialClass = 'C2';
            } elseif ($points >= 23 && $points <= 28) {
                $socialClass = 'C1';
            } elseif ($points >= 29 && $points <= 37) {
                $socialClass = 'B2';
            } elseif ($points >= 38 && $points <= 44) {
                $socialClass = 'B1';
            } elseif ($points >= 45 && $points <= 100) {
                $socialClass = 'A';
            } else {
                $socialClass = 'INVALID';
            }

            $response = self::executeSweetApi(
                'PUT',
                '/api/social-class/v1/frontend/final/'.$final->data[0]->id,
                [
                    'customers_id' => $final->data[0]->customers_id,
                    'final_points' => $final->data[0]->final_points,
                    'final_class_by_income' => 'NULL',
                    'final_class_by_questions' => $socialClass,
                    'earned_points' => 60
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

    private static function updateCustomerPoints() {
        $customerId = session()->get('id');

        try {

            $getCustomer = self::executeSweetApi(
                'POST',
                '/api/v1/frontend/customers/'.$customerId,
                []
            );

            $customer = $getCustomer->customer;
            $customer->points = $customer->points + 60;

            $updateCustomer = self::executeSweetApi(
                'PUT',
                '/api/v1/frontend/customers/'.$customerId,
                get_object_vars($customer)                
            );

            return $updateCustomer->result->points;
            
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

    private static function notAnswered($responses) {
        try {
            $finalData = [];
            foreach($responses->data as $r) {
                $getAnswer = self::executeSweetApi(
                    'GET',
                    '/api/social-class/v1/frontend/research-answer?where[question_id]='.$r->id.'&where[customers_id]='.session()->get('id'),
                    []
                );
    
                if(false === isset($getAnswer->data[0]->id)) {
                    array_push($finalData, $r);
                }
            }
    
            return $finalData;

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