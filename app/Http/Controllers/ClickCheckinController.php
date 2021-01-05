<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Checkin;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\ActionTypeMeta;
use Illuminate\Support\Facades\Validator;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class ClickCheckinController extends Controller
{
    private $rules = [
        'action_id'   => 'required|numeric',
        'customer_id' => 'required|numeric',
    ];

    public function handle(Request $request)
    {
        /**
         * Retrieves incoming input.
         */
        $data = $request->only([
            'action_id',
            'customer_id',
        ]);

        /**
         * Validates incoming input.
         */
        $validation = Validator::make($data, $this->rules);

        if ($validation->fails()) 
        {
            return redirect('/')->with('alert', [
                'type'    => 'danger',
                'message' => 'Ação ou Usuário inválidos.',
            ]);
        }

        /**
         * Find the `action`.
         */
        $action = Action::find($data['action_id']);

        if (empty($action)) 
        {
            return redirect('/')->with('alert', [
                'type'    => 'danger',
                'message' => 'Ação inválida.',
            ]);
        }

        /**
         * Find the `action_type_meta`.
         */
        $actionTypeMeta = ActionTypeMeta::find($action->id);

        if (empty($actionTypeMeta)) 
        {
            return redirect('/')->with('alert', [
                'type'    => 'danger',
                'message' => 'Ação inválida.',
            ]);
        }        

        /**
         * Find the `customer`.
         */
        $customer = Customer::find($data['customer_id']);

        if (is_null($customer)) 
        {
            return redirect('/')->with('alert', [
                'type'    => 'danger',
                'message' => 'Usuário inválido.',
            ]);
        }

        /**
         * Check if this `customer` already checkedin this `action`.
         */
        $checkin = Checkin::where('customers_id', $customer->id)
                    ->where('actions_id', $action->id)
                    ->first();

        if (false === empty($checkin)) 
        {
            Log::debug('Os pontos para o usuário ' . $customer->id . ' já foram computados para a ação ' . $action->id);

            return redirect($actionTypeMeta->value);

        }

        $saved = false;
        
        /**
         * Give `action` points to the `customer`.
         */
        try {
            $checkin               = new Checkin();
            $checkin->actions_id   = $action->id;
            $checkin->customers_id = $customer->id;
            $checkin->points       = $action->grant_points;
            $checkin->save();
            $saved=true;

        }
        catch (QueryException $e)
        {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062) {
                Log::debug("Checking already on database -> {$e->getMessage()}");
            }

        } 
        catch (PDOException $e) 
        {
            Log::debug("Database Problem -> {$e->getMessage()}");
        }

        if($saved)
        {
            try 
            {
                $customer->points = $customer->points + $action->grant_points;
                $customer->save();
            }
            catch (PDOException $e) 
            {
                Log::debug("Database Problem -> {$e->getMessage()}");
            }
        }

        /**
         * Redirect the `customer`.
         */
        return redirect($actionTypeMeta->value);

    }
}
