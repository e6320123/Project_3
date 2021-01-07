<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Game_list;
use App\News_list;

class GameController extends Controller
{
    function index(){  
        $gameAry = Game_list::all();
        $newsAry = News_list::all();
        return view("index",compact('gameAry','newsAry')); 
    } 
    
    function link($fname){  

        $fname = "link.".$fname;
        
        return view($fname); 
    } 
}
