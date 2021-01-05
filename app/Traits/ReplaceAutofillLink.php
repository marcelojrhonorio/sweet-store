<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait ReplaceAutofillLink
{
    private static function handleReplaceLink ($link) {
        // Log::debug('link before: ' . $link);
        $link = str_replace("##nm_id##", urlencode(session()->get('id')), $link);
        $link = str_replace("##nm_fullname##", urlencode(session()->get('name')), $link);
        $link = str_replace("##nm_email##", urlencode(session()->get('email')), $link);
        $link = str_replace("##nm_birthdate##", urlencode(self::formatBirthdate()), $link);
        $link = str_replace("##nm_phone##", urlencode(self::getPhoneNumber()) , $link);
        // Log::debug('link after: ' . $link);

        return $link;
    }

    private static function formatBirthdate ()
    {
        $birthdate = session()->get('birthdate');
        $year  = substr($birthdate, 0, 4);
        $month = substr($birthdate, 5, 2);
        $day   = substr($birthdate, 8, 2);

        $finalString = (string) ($day . "/" . $month . "/" . $year);

        return $finalString;
    }

    private static function getPhoneNumber()
    {
        $phone = session()->get('phone_number');
        $ddd   = session()->get('ddd');

        // Caso já tenha o DDD no field.
        if (strpos($phone, '(') === true && strpos($phone, ')') === true) {
            return $phone;
        }

        // Caso o valor de phone contenha letras.
        if (preg_match("/[a-z]/i", $phone)) {
            return " ";
        }

        // Caso o valor de phone esteja vazio.
        if ('' === $phone || null === $phone) {
            return " ";
        }

        // Caso o valor de DDD esteja vazio e não entrou na condição acima.
        if ('' === $ddd || null === $ddd) {
            return " ";
        }
        
        // Caso o campo DDD tenha menos de 2 caracteres.
        if (strlen($ddd < 2)) {
            return " ";
        }

        return "({$ddd}){$phone}";
    }
}