<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Jobs\CustomerConfirmationResend;
use Illuminate\Support\Facades\Validator;

class ProfileEmailController extends Controller
{
    public function resend(Request $request)
    {
        $email    = session('email');
        $customer = Customer::where('email', $email)->first();

        if (is_null($customer)) {
            return response()->json([
                'success' => false,
                'errors'  => ['invalid' => 'Customer não encontrado'],
                'data'    => [],
            ]);
        }

        $condition = ($email != $request->input('email'));
        $newCustomer = Customer::where('email', $request->input('email'))->first();

        if (!is_null($newCustomer) && $condition) {
            return response()->json([
                'success' => false,
                'errors'  => ['invalid' => 'Este e-mail pertence a outra conta.'],
                'data'    => [],
            ]);
        }

        $customer->resend_attempts = $customer->resend_attempts + 1;

        if ($customer->resend_attempts > 2) {
            return response()->json([
                'success' => false,
                'errors'  => ['limit' => 'Número de tentativas esgotado.'],
                'data'    => [],
            ]);
        }

        $customer->save();

        CustomerConfirmationResend::dispatch($customer)->onQueue('confirmation_resend');

        return response()->json([
            'success' => true,
            'data'    => $customer,
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->only('email'), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => ['invalid' => 'O campo e-mail é obrigatório'],
                'data'    => [],
            ]);
        }

        $oldEmail = session('email');
        $customer = Customer::where('email', $oldEmail)->first();

        if (is_null($customer)) {
            return response()->json([
                'success' => false,
                'errors'  => ['invalid' => 'Customer não encontrado'],
                'data'    => [],
            ]);
        }

        $condition = ($oldEmail != $request->input('email'));
        $newCustomer = Customer::where('email', $request->input('email'))->first();

        if (!is_null($newCustomer) && $condition) {
            return response()->json([
                'success' => false,
                'errors'  => ['invalid' => 'Este e-mail pertence a outra conta.'],
                'data'    => [],
            ]);
        }

        $customer->email = $request->input('email');

        $customer->save();

        $request->session()->put('email', $customer->email);

        CustomerConfirmationResend::dispatch($customer)->onQueue('confirmation_resend');

        return response()->json([
            'success' => true,
            'data'    => $customer,
        ]);
    }
}
