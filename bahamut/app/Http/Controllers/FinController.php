<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinController extends Controller
{
    function index(){

        return view('financial.index');

    }

    function calc(){

        return view('financial.calc');

    }
}
