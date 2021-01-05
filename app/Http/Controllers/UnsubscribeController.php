<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Traits\SweetStaticApiTrait;

class UnsubscribeController extends Controller
{
    use SweetStaticApiTrait;

    public function index (Request $request) 
    {
        $email = $request->query('email') ?: session('email');

        if (empty($email)) {
            return redirect('/');
        }

        $customerNowLogged = !session('email') ? self::login($email) : null;

        if ($customerNowLogged && 'unauthorized_login' == $customerNowLogged->status) {
            return redirect('/');
        }

        if ($customerNowLogged && 'authorized_login' == $customerNowLogged->status) {
            self::setCurrentUser($customerNowLogged->data);
        }

        return view('unsubscribe.index');
    }

    public function unsubscribe(Request $request)
    {
        $params = self::getParams($request);

        if (null === $params['final_option']) {
            return response()->json([
                'success' => false,
                'status'  => 'without_final_option',
                'message' => 'Opção final não informada.',
                'data' => [],
            ]);
        }

        if (null === $params['reasons']) {
            return response()->json([
                'success' => false,
                'status'  => 'without_reasons',
                'message' => 'Nenhum motivo para descadastro/cancelamento foi informado.',
                'data' => [],
            ]);
        }

        if (preg_match("/(?:7)/", $params['reasons']) && null === $params['another_reason_description']) {
            return response()->json([
                'success' => false,
                'status'  => 'without_another_reason_description',
                'message' => 'Foi marcada a opção Outro, mas não especificado o motivo',
                'data' => [],
            ]);
        }

        /**
         * Verify if already exists unsubscribe.
         */
        $alreadyUnsubscribed = self::getUnsubscribed(session('id'));

        $c1 = isset($alreadyUnsubscribed->data[0]->final_option);
        $c2 = $c1 && $alreadyUnsubscribed->data[0]->final_option === $params['final_option'];

        if($c1 && $c2) {
            return response()->json([
                'success' => false,
                'status'  => 'already_unsubscribed',
                'message' => 'Já foi feita uma solicitação de desinscrição.',
                'data' => $alreadyUnsubscribed->data[0],
            ]);
        }

        $uId = $alreadyUnsubscribed->data[0]->id ?? null;
        $unsubscribedCustomer = ($c1 && $c2) ? self::updateUnsubscribe($params, $uId) : self::unsubscribeApi($params);

        if (null === $unsubscribedCustomer) {
            return response()->json([
                'success' => false,
                'status'  => 'unkdown_error',
                'message' => 'Algo de errado ocorreu ao gravar o registro',
                'data' => $unsubscribedCustomer->data,
            ]);
        }

        if ($params['final_option'] === 'delete_account') {
            $cId = session('id');
            self::logout();
            self::deleteCustomer($cId);
        }

        return response()->json([
            'success' => true,
            'status'  => 'success',
            'message' => 'Descadastro realizado com sucesso',
            'data' => $unsubscribedCustomer->data,
        ]);
    }

    private static function getCustomer (String $email) {
        $response = self::executeSweetApi(
            'GET',
            '/api/v1/frontend/customers/find-email',
            ['email' => $email]
        );

        return $response->data ?? null;
    }

    private static function login (String $email) {

        $response = self::executeSweetApi(
            'GET',
            '/api/v1/customers/click-login?email=' . $email,
            []
        );

        return $response;
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
            'clicks_share_mail' => $customer->clicks_share_mail,
        ]);
    }

    private static function getParams ($request) {
        return [
            'customers_id' => session('id'),
            'another_reason_description' => $request->input('data')['another_reason_description'],
            'final_option' => $request->input('data')['final_option'],
            'suggestion' => $request->input('data')['suggestion'],
            'reasons' => $request->input('data')['reasons']
        ];
    }

    private static function unsubscribeApi($params) {
        $response = self::executeSweetApi(
            'POST',
            '/api/unsubscribed/v1/frontend/unsubscribed-customers',
            $params
        );

        return $response ?? null;
    }

    private static function updateUnsubscribe ($params, int $id) {
        $response = self::executeSweetApi(
            'PUT',
            '/api/unsubscribed/v1/frontend/unsubscribed-customers/' . $id,
            $params
        );

        return $response ?? null;
    }

    private static function getUnsubscribed (int $customerId) {
        $response = self::executeSweetApi(
            'GET',
            '/api/unsubscribed/v1/frontend/unsubscribed-customers?where[customers_id]=' . $customerId,
            []
        );

        return $response ?? null;
    }

    private static function logout () {
        $data = [
            'email' => session('email'),
            'token' => session('token'),
        ];

        $response = self::executeSweetApi(
            'POST',
            '/api/v1/customers/logout',
            $data
        );

        session()->flush();
        
        return $response ?? null;

    }

    private static function deleteCustomer (int $customerId) {
        $response = self::executeSweetApi(
            'DELETE',
            '/api/v1/frontend/customers/' . $customerId,
            []
        );

        return $response ?? null;
    }
}
