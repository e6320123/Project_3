<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OneController extends Controller
{
    function financial(){
        return view('financial');
    }
    function calc(){
        return view('calc');
    }
}
