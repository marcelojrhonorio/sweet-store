<?php

namespace App\Http\Controllers\Exchange;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExchangePointsController extends Controller
{
    /**
     * @todo Add docs.
     */
    public function index(Request $request)
    {
        /**
         * @todo Add docs.
         */
        return view('exchange');
    }
}
