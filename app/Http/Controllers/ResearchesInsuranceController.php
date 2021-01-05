<?php

namespace App\Http\Controllers;

use App\Events\CarInsuranceCreated;
use App\Models\Customer;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ResearchesInsuranceController extends Controller
{
    private $endpointResearches = '';

    private $endpointAnswers = '';

    private $client;

    public function __construct()
    {
        $base = env('APP_SWEET_API') . '/api/seguroauto/v1/frontend';

        $this->endpointResearches = $base . '/customer-researches';
        $this->endpointAnswers = $base . '/customer-research-answers';

        $this->client = new Client();
    }

    public function stepOne(Request $request)
    {
        $isValidCep = preg_match('/^\d{2}\.\d{3}-\d{3}$/', $request->input('cep'));

        if (!$isValidCep) {
            return response()->json([
                'success' => false,
                'message' => 'CEP inválido.',
                'data' => [],
            ]);
        }

        $id = session('id');
        $customer = Customer::find($id);

        if (empty($customer)) {
            return response()->json([
                'success' => false,
                'message' => 'Customer não encontrado.',
                'data' => [],
            ]);
        }

        $customer->cep = $request->input('cep');
        $customer->city = $request->input('city');
        $customer->state = $request->input('state');

        $customer->save();

        session(['cep' => $customer->cep]);

        return response()->json([
            'success' => true,
            'message' => 'CEP atualizado com sucesso.',
            'data' => $customer,
        ]);
    }

    public function stepTwo(Request $request)
    {
        // Não tem carro:
        // 1. Cria pesquisa.
        // 2. Retorna pesquisa.
        //
        // Tem carro:
        // 1. Cria pesquisa.
        // 2. Cria resposta.
        // 3. Retorna pesquisa e resposta.

        // Respostas da pesquisa.
        $answerData['customer_research_answer_has_insurance'] = $request->input('hasInsurance');
        $answerData['insurance_company_id'] = $request->input('insurer');
        $answerData['model_year_id'] = $request->input('year');
        $answerData['customer_research_answer_date_insurace_at'] = $request->input('dateInsurance');
        $answerData['customer_research_answer_status_sicronized'] = 0;

        // Tem carro?
        $hasCar = $request->input('hasCar');

        // Vai atualizar a pesquisa ou criar uma nova?
        $hasResearch = $this->getResearches();

        if (!$hasResearch->total) {
            $research = $this->createResearch();

            if ($hasCar) { // Se tem carro, cria resposta...
                $answerData['customer_research_id'] = $research->id;
                $createdAnswer = $this->createAnswer($answerData);

                return response()->json([
                    'success' => true,
                    'message' => 'Pesquisa e Resposta cadastradas com sucesso.',
                    'data' => [
                        'research' => $research,
                        'answer' => $createdAnswer ?? [],
                    ],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pesquisa cadastrada com sucesso.',
                'data' => [
                    'research' => $research,

                ],
            ]);
        }

        $research = $hasResearch->data[0];

        if ($hasCar) {
            $answer = $this->getAnswer($research->id);
            if ($answer->total) {
                $dataAnswer = $answer->data[0];
                $answerData['customer_research_id'] = $research->id;
                $answerData['id'] = $dataAnswer->id;

                $updatedAnswer = $this->updateAnswer($dataAnswer->id, $answerData);

                return response()->json([
                    'success' => true,
                    'message' => 'Pesquisa e Resposta atualizada com sucesso.',
                    'data' => [
                        'research' => $research,
                        'answer' => $updatedAnswer,
                    ],
                ]);
            }
            $answerData['customer_research_id'] = $research->id;
            $createdAnswer = $this->createAnswer($answerData);
            return response()->json([
                'success' => true,
                'message' => 'Pesquisa atualizada e Resposta cadastrada com sucesso.',
                'data' => [
                    'research' => $research,
                    'answer' => $createdAnswer ?? [],
                ],
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Pesquisa atualizada com sucesso.',
            'data' => [
                'research' => $research,
            ],
        ]);
    }

    public function stepThree(Request $request)
    {
        $inputs = $request->only(['cpf', 'cell', 'phone']);

        $rules = [
            'cpf' => 'required|cpf',
            'cell' => 'required|celular_com_ddd',
        ];

        $validation = Validator::make($inputs, $rules);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Celular e/ou CPF inválidos.',
                'errors' => $validation->errors(),
                'data' => [],
            ]);
        }

        $id = session('id');
        $customer = Customer::find($id);

        if (empty($customer)) {
            return response()->json([
                'success' => false,
                'message' => 'Customer não encontrado.',
                'data' => [],
            ]);
        }

        $hasResearch = $this->getResearches();

        if ($hasResearch->total) {
            $research = $hasResearch->data[0];

            $researchData = [
                'completed' => 1,
                'customer_id' => session('id'),
                'customer_research_points' => 100,
            ];

            $updatedResearch = $this->updateResearch($research->id, $researchData);
        }

        $customer->cpf = $request->input('cpf') ?? $customer->cpf;
        $customer->points += 100;
        $customer->phone_number = $request->input('cell') ?? $customer->phone_number;
        $customer->secondary_phone_number = $request->input('phone') ?? $customer->secondary_phone_number;

        $customer->save();

        session(['points' => $customer->points]);

        if (100 >= $this->getTotalLeads() &&
            $this->verifyCustomerLead($customer->id) &&
            env('SEGURO_AUTO_LEAD_SEND')) {
            event(new CarInsuranceCreated($customer->id));
        }

        return response()->json([
            'success' => true,
            'message' => 'Dados atualizados com sucesso.',
            'data' => [
                'customer' => $customer,
                'updatedResearch' => $updatedResearch ?? null,
            ],
        ]);
    }

    private function getResearches()
    {
        $clientGetResearches = new Client();

        $endpoint = env('APP_SWEET_API') . '/api/seguroauto/v1/frontend/customer-researches';

        $response = $clientGetResearches->get($endpoint . '?where[customer_id]=' . session('id'));

        $content = $response->getBody()->getContents();

        $contentJson = \GuzzleHttp\json_decode($content);

        return $contentJson;
    }

    private function getTotalLeads()
    {
        $clientGetTotalLeads = new Client();

        $endpoint = env('APP_SWEET_API') . '/api/seguroauto/v1/frontend/veem-leads?where[creation_date]=' . Carbon::today()->toDateString() . '&where[lead_sicronized]=1&limit=51';

        $response = $clientGetTotalLeads->get($endpoint);

        $content = $response->getBody()->getContents();

        $contentJson = \GuzzleHttp\json_decode($content);

        return $contentJson->total;
    }

    private function verifyCustomerLead($verifyCustomerLead = 0)
    {
        $clientVerifyCustomerLead = new Client();

        $endpoint = env('APP_SWEET_API') . '/api/seguroauto/v1/frontend/veem-leads';

        $response = $clientVerifyCustomerLead->get("{$endpoint}?where[customer_id]={$verifyCustomerLead}");

        $content = $response->getBody()->getContents();

        $contentJson = \GuzzleHttp\json_decode($content);

        return $contentJson->total;
    }

    private function updateResearch($researchId, array $data = [])
    {
        $clientUpdateResearch = new Client();

        $endpoint = env('APP_SWEET_API') . '/api/seguroauto/v1/frontend/customer-researches/' . $researchId;

        $response = $clientUpdateResearch
            ->request(
                'PUT',
                $endpoint,
                ['form_params' => $data]
            );

        $content = $response->getBody()->getContents();

        $dataJson = \GuzzleHttp\json_decode($content)->data;

        return $dataJson;
    }

    private function createResearch()
    {
        $clientCreateResearch = new Client();

        $response = $clientCreateResearch->request('POST', $this->endpointResearches, [
            'form_params' => [
                'customer_id' => session('id'),
                'customer_research_points' => 100,
            ],
        ]);

        $content = $response->getBody()->getContents();

        $contentJson = \GuzzleHttp\json_decode($content);

        return $contentJson->data;
    }

    private function getAnswer($researchId)
    {
        $clientGetAnswer = new Client();

        $endpoint = env('APP_SWEET_API') . '/api/seguroauto/v1/frontend/customer-research-answers';

        $response = $clientGetAnswer->get($endpoint . '?where[customer_research_id]=' . $researchId);

        $content = $response->getBody()->getContents();

        $dataJson = \GuzzleHttp\json_decode($content);

        return $dataJson;
    }

    private function updateAnswer($answerId, array $data = [])
    {
        $clientUpdateAnswer = new Client();

        $endpoint = env('APP_SWEET_API') . '/api/seguroauto/v1/frontend/customer-research-answers/' . $answerId;

        $response = $clientUpdateAnswer->request('PUT', $endpoint, [
            'form_params' => $data,
        ]);

        $content = $response->getBody()->getContents();

        $dataJson = \GuzzleHttp\json_decode($content)->data;

        return $dataJson;
    }

    private function createAnswer(array $data = [])
    {
        $clientCreateAnswer = new Client();

        $response = $clientCreateAnswer->request('POST', $this->endpointAnswers, [
            'form_params' => $data,
        ]);

        $content = $response->getBody()->getContents();

        $contentJson = \GuzzleHttp\json_decode($content);

        return $contentJson->data;
    }

    private function updateCustomer(array $data = [])
    {
        $customer = Customer::find($data['id']);

        $customer->cep = $data['cep'];
        $customer->cpf = $data['cpf'];
        $customer->phone_number = $data['cell'] ?? $customer->phone_number;
        $customer->secondary_phone_number = $data['phone'];

        $customer->points += 5;

        $customer->save();

        session(['points' => $customer->points]);

        return $customer->toJson();
    }

    public function leadDispatch(Request $request)
    {
        $id = $request->input('customer_id');
        event(new CarInsuranceCreated($id));        
    }
}
