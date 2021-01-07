<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Game_list;
use App\News_list;
use DB;

class GameController extends Controller
{
    function index(){  
        // dd(DB::getQueryLog())    //to see what database queries were run.
        $chg_game = 1;
        $gameAry = Game_list::all();
        $newsAry = News_list::all();
        // $gameAry = DB::table('game_lists')->where('name', 'like', '%2%')->get();
        // $newsAry = DB::table('news_lists')->get(); 
        
        // return view("echo",compact('gameAry','newsAry','chg_game')); 
        return view("index",compact('gameAry','newsAry')); 
    } 
    
    function link($fname){  

        $fname = "link.".$fname;
        
        return view($fname); 
    } 
}
