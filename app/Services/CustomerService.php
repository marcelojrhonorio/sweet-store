<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService
{
    public static function setUserSession(Customer $c = null)
    {
        if ($c) {
            session([
                'id' => $c->id,
                'name' => $c->fullname,
                'email' => $c->email,
                'birthdate' => $c->birthdate,
                'gender' => $c->gender,
                'cep' => $c->cep,
                'avatar' => $c->avatar,
                'points' => $c->points,
                'token' => $c->token,
                'confirmed' => $c->confirmed,
            ]);
        }
    }

    public static function renewToken(Customer $c = null)
    {
        if ($c) {
            $c->token = base64_encode(str_random(40));
            return $c->update();
        }
        return null;
    }
}
